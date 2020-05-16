<?php

namespace App\Service;

class IpHelper
{
    public function isInRange(string $ip, string $range)
    {
        if (strpos($range, '/') === false) {
            return (bool)preg_match('/' . $range . '/', $ip);
        }

        list($net, $maskbits) = explode('/', $range);
        $binaryIp = $this->iptToBits($ip);
        $binaryNet = $this->iptToBits($net);

        $ipNetBits = substr($binaryIp, 0, $maskbits);
        $netBits   = substr($binaryNet, 0, $maskbits);

        return $ipNetBits === $netBits;
    }

    protected function iptToBits($ip)
    {
        $inet = inet_pton($ip);
        $chars = str_split($inet);
        $binaryip = '';
        foreach ($chars as $char) {
            $binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        return $binaryip;
    }
}
