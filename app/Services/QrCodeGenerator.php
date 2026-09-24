<?php

declare(strict_types=1);

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Generator;
use Stringable;

/**
 * Renders QR codes as SVG or PNG.
 *
 * simple-qrcode renders PNG through Imagick. Many small VPSes only ship GD,
 * so PNG falls back to drawing the same bacon/bacon-qr-code matrix with GD.
 */
final readonly class QrCodeGenerator
{
    private const int MARGIN_MODULES = 2;

    public function __construct(private Generator $generator) {}

    public function svg(string $content, int $size = 512): string
    {
        return $this->stringify((clone $this->generator)
            ->format('svg')
            ->size($size)
            ->margin(self::MARGIN_MODULES)
            ->errorCorrection('M')
            ->encoding('UTF-8')
            ->generate($content));
    }

    public function png(string $content, int $size = 1024): string
    {
        if (extension_loaded('imagick')) {
            return $this->stringify((clone $this->generator)
                ->format('png')
                ->size($size)
                ->margin(self::MARGIN_MODULES)
                ->errorCorrection('M')
                ->encoding('UTF-8')
                ->generate($content));
        }

        return $this->pngWithGd($content, $size);
    }

    /**
     * simple-qrcode returns an HtmlString (or a string); its docblock is imprecise.
     */
    private function stringify(mixed $result): string
    {
        return match (true) {
            is_string($result) => $result,
            $result instanceof Stringable => (string) $result,
            default => throw new RuntimeException('Unable to render the QR code.'),
        };
    }

    private function pngWithGd(string $content, int $size): string
    {
        $matrix = Encoder::encode($content, ErrorCorrectionLevel::M(), 'UTF-8')->getMatrix();
        $modules = $matrix->getWidth();
        $total = $modules + 2 * self::MARGIN_MODULES;
        $scale = max(1, intdiv($size, $total));
        $pixels = max(1, $total * $scale);

        $image = imagecreate($pixels, $pixels);

        if ($image === false) {
            throw new RuntimeException('Unable to allocate the QR code image.');
        }

        imagecolorallocate($image, 255, 255, 255);
        $black = (int) imagecolorallocate($image, 0, 0, 0);

        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $left = ($x + self::MARGIN_MODULES) * $scale;
                    $top = ($y + self::MARGIN_MODULES) * $scale;
                    imagefilledrectangle($image, $left, $top, $left + $scale - 1, $top + $scale - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($image);

        return (string) ob_get_clean();
    }
}
