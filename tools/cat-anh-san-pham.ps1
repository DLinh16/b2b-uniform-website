# Cat anh san pham ve cung ti le 4:5 va nen nho lai.
# Dung .NET System.Drawing (co san tren Windows), khong can GD/Imagick cua PHP.
#
# Cach chay (PowerShell):
#   powershell -ExecutionPolicy Bypass -File tools\cat-anh-san-pham.ps1 [-Site C:\xampp\htdocs\dongphuc]
#
# Ket qua: wp-content/uploads/vu-sanpham/<SKU>.jpg (toi da 1000x1250)
#          wp-content/uploads/vu-sanpham/<SKU>-600x750.jpg (anh thu nho cho luoi san pham)

param([string]$Site = "C:\xampp\htdocs\dongphuc")

Add-Type -AssemblyName System.Drawing

$up  = Join-Path $Site "wp-content\uploads"
$out = Join-Path $up "vu-sanpham"
New-Item -ItemType Directory -Force $out | Out-Null

# SKU => anh nguon (tuong doi voi uploads), focusY: 0 = giu phan tren, 0.5 = giua
$map = [ordered]@{
    "CS-001" = @("2026\11\CS-001.jpg", 0.30)
    "CS-002" = @("2026\11\CS-002.jpg", 0.30)
    "CS-003" = @("2026\11\ao-somi.jpg", 0.20)
    "PL-001" = @("2026\11\PL-001.jpg", 0.30)
    "PL-002" = @("2026\11\PL-002.jpg", 0.50)
    "PL-003" = @("anhthat\aothun.jpg", 0.30)
    "YT-001" = @("2026\11\YT-001.jpg", 0.50)
    "YT-002" = @("2026\11\YT-002.jpg", 0.50)
    "YT-003" = @("anhthat\yte.jpg", 0.30)
    "NH-001" = @("2026\11\NH-001.jpg", 0.30)
    "NH-002" = @("2026\11\NH-002.jpg", 0.30)
    "NH-003" = @("anhthat\khachhsan.jpg", 0.30)
    "BH-001" = @("2026\11\BH-001.jpg", 0.30)
    "BH-002" = @("2026\11\BH-002.jpg", 0.30)
    "BH-003" = @("anhthat\baoho.jpg", 0.50)
    "HS-001" = @("anhthat\truonghoc.jpg", 0.30)
}

$jpeg = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq "image/jpeg" }
$enc  = New-Object System.Drawing.Imaging.EncoderParameters(1)
$enc.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]82)

function Save-Crop($img, [int]$cx, [int]$cy, [int]$cw, [int]$ch, [int]$tw, [int]$th, [string]$path) {
    $bmp = New-Object System.Drawing.Bitmap($tw, $th)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.InterpolationMode  = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode      = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $g.PixelOffsetMode    = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    $attr = New-Object System.Drawing.Imaging.ImageAttributes
    $attr.SetWrapMode([System.Drawing.Drawing2D.WrapMode]::TileFlipXY)
    $g.DrawImage($img, (New-Object System.Drawing.Rectangle(0, 0, $tw, $th)), $cx, $cy, $cw, $ch, [System.Drawing.GraphicsUnit]::Pixel, $attr)
    $bmp.Save($path, $jpeg, $enc)
    $g.Dispose(); $bmp.Dispose()
}

foreach ($sku in $map.Keys) {
    $src = Join-Path $up $map[$sku][0]
    $fy  = [double]$map[$sku][1]
    if (-not (Test-Path $src)) { Write-Warning "Thieu anh: $src"; continue }
    $img = [System.Drawing.Image]::FromFile($src)

    # Vung cat ti le 4:5 lon nhat
    $w = $img.Width; $h = $img.Height
    if ($w / $h -gt 0.8) { $ch = $h; $cw = [int][math]::Round($h * 0.8) } else { $cw = $w; $ch = [int][math]::Round($w / 0.8) }
    $cx = [int][math]::Round(($w - $cw) / 2)
    $cy = [int][math]::Round(($h - $ch) * $fy)

    $fw = [math]::Min(1000, $cw); $fh = [int][math]::Round($fw * 1.25)
    Save-Crop $img $cx $cy $cw $ch $fw $fh (Join-Path $out "$sku.jpg")
    Save-Crop $img $cx $cy $cw $ch 600 750 (Join-Path $out "$sku-600x750.jpg")
    $img.Dispose()
    Write-Output ("{0}`t{1}x{2}" -f $sku, $fw, $fh)
}
