<?php

namespace Emc\Otp\Api;

/**
 * Interface GenerateOtpInterface
 *
 * @package Emc\Otp\Api
 */
interface GenerateOtpInterface {

    /**
     * Generate OTP
     *
     * @param string $telephone
     * @param string|null $code
     * @param string|null $type
     * @param bool $resend
     * @return string $items
     */
    public function generateOtp($telephone,  $code = null,  $type = 'OTP',  $resend = false);

    /**
     * validate OTP
     *
     * @param string $telephone
     * @param string|null $otp
     * @return string $items
     */
    public function validateOtp( $telephone,  $otp);
}
