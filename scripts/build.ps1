param([string]$SourcePath = '')
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$slug = 'calico-gravity-forms-styler'
if (-not $SourcePath) {
    if (Test-Path -LiteralPath (Join-Path $projectRoot "$slug.php")) { $SourcePath = $projectRoot }
    else { $SourcePath = Join-Path $projectRoot "source\$slug" }
}
$source = (Resolve-Path -LiteralPath $SourcePath).Path
$header = Get-Content -LiteralPath (Join-Path $source "$slug.php") -Raw
if ($header -notmatch 'Version:\s*([0-9.]+)') { throw 'Version header missing' }
$version = $Matches[1]
if ($header -notmatch ("define\( 'CGFS_VERSION', '" + [regex]::Escape($version) + "' \)")) { throw 'Runtime version mismatch' }
$readme = Get-Content -LiteralPath (Join-Path $source 'readme.txt') -Raw
if ($readme -notmatch ('Stable tag:\s*' + [regex]::Escape($version) + '\s')) { throw 'Readme version mismatch' }
$allowed = @("$slug.php", 'includes/widget.php', 'assets/styler.css', 'readme.txt', 'LICENSE')
$dist = Join-Path $projectRoot "dist\v$version"
New-Item -ItemType Directory -Path $dist -Force | Out-Null
$output = Join-Path $dist "$slug.zip"
if (Test-Path -LiteralPath $output) { throw 'Package exists. Preserve prior artifacts; increment the version or choose a fresh workspace.' }
$staging = Join-Path $projectRoot ('build\' + [guid]::NewGuid().ToString('N'))
foreach ($relative in $allowed) {
    $origin = Join-Path $source $relative
    if (-not (Test-Path -LiteralPath $origin -PathType Leaf)) { throw "Missing production file: $relative" }
    $destination = Join-Path $staging "$slug\$relative"
    New-Item -ItemType Directory -Path (Split-Path -Parent $destination) -Force | Out-Null
    Copy-Item -LiteralPath $origin -Destination $destination
}
& tar.exe -a -c -f $output -C $staging $slug
if ($LASTEXITCODE -ne 0) { throw 'ZIP creation failed' }
Add-Type -AssemblyName System.IO.Compression.FileSystem
$archive = [System.IO.Compression.ZipFile]::OpenRead($output)
$manifest = @()
try {
    $files = @($archive.Entries | Where-Object { $_.Name.Length -gt 0 })
    if ($files.Count -ne $allowed.Count) { throw 'Unexpected package file count' }
    foreach ($entry in $files) {
        $name = $entry.FullName.Replace('\','/')
        if (-not $name.StartsWith("$slug/")) { throw "Invalid archive root: $name" }
        $relative = $name.Substring($slug.Length + 1)
        if ($allowed -notcontains $relative) { throw "Unexpected production file: $name" }
        $stream = $entry.Open()
        $sha = [System.Security.Cryptography.SHA256]::Create()
        try { $actual = ([BitConverter]::ToString($sha.ComputeHash($stream))).Replace('-','') }
        finally { $stream.Dispose(); $sha.Dispose() }
        $expected = (Get-FileHash -LiteralPath (Join-Path $source $relative) -Algorithm SHA256).Hash
        if ($actual -ne $expected) { throw "ZIP/source mismatch: $relative" }
        $manifest += [ordered]@{ path = $name; bytes = $entry.Length; sha256 = $actual }
    }
} finally { $archive.Dispose() }
$package = Get-Item -LiteralPath $output
$record = [ordered]@{ version = $version; asset = "$slug.zip"; bytes = $package.Length; sha256 = (Get-FileHash -LiteralPath $output -Algorithm SHA256).Hash; files = $manifest }
$record | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath (Join-Path $dist 'package-manifest.json') -Encoding UTF8
$record | ConvertTo-Json -Depth 5
