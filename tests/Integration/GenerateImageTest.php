<?php

namespace Nikazooz\LaravelCaptcha\Tests\Integration;

use Nikazooz\LaravelCaptcha\Facades\Captcha;
use Nikazooz\LaravelCaptcha\Tests\IntegrationTest;

class GenerateImageTest extends IntegrationTest
{
    public function test_can_generate_captcha_image_when_requested_using_gd()
    {
        if (! function_exists('gd_info')) {
            $this->markTestSkipped(
                'The GD extension is not available.'
            );
        }

        $this->setConfig('driver', 'gd');

        $response = $this->withoutExceptionHandling()->get(Captcha::url());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        $imageContent = $response->getContent();
        $this->assertNotEmpty($imageContent);

        list(, , $type) = getimagesizefromstring($imageContent);
        $expectedType = 3; // PNG
        $this->assertEquals($expectedType, $type);
    }

    public function test_can_generate_captcha_image_when_requested_using_imagick()
    {
        if (! class_exists('Imagick')) {
            $this->markTestSkipped(
                'The Imagick extension is not available.'
            );
        }

        $this->setConfig('driver', 'imagick');

        $response = $this->withoutExceptionHandling()->get(Captcha::url());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        $imageContent = $response->getContent();
        $this->assertNotEmpty($imageContent);

        list(, , $type) = getimagesizefromstring($imageContent);
        $expectedType = 3; // PNG
        $this->assertEquals($expectedType, $type);
    }

    public function test_can_configure_image_width_and_height()
    {
        if (! function_exists('gd_info')) {
            $this->markTestSkipped(
                'The GD extension is not available.'
            );
        }

        $this->setConfig('driver', 'gd');

        $this->setConfig('image.width', 500);
        $this->setConfig('image.height', 100);

        $response = $this->withoutExceptionHandling()->get(Captcha::url());

        $imageContent = $response->getContent();

        list($width, $height) = getimagesizefromstring($imageContent);
        $this->assertEquals(500, $width);
        $this->assertEquals(100, $height);
    }
}
