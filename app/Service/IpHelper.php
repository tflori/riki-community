<?php

namespace App\Service;

class IpHelper
{
    /**
     * Check if the $ip is in $range
     *
     * Accepted range definitions:
     * - an ip address (e. g. 192.168.0.1)
     * - an ipv4 range (e. g. 10.23.0.0/16)
     * - an ipv6 range (e. g. fe80::/64)
     * - a regular expression (e. g. 192.168.0.\d+)
     *
     * @param string $ip
     * @param string $range
     * @return bool
     */
    public function isInRange(string $ip, string $range): bool
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

    /**
     * Get a string of bits from ip address
     *
     * Works for ipv4 and ipv6 addresses
     *
     * @param $ip
     * @return string
     */
    protected function iptToBits($ip): string
    {
        $inet = inet_pton($ip);
        $chars = str_split($inet);
        $binaryIp = '';
        foreach ($chars as $char) {
            $binaryChar = decbin(ord($char));
            // fill left with 0es
            $binaryIp .= str_pad($binaryChar, 8, '0', STR_PAD_LEFT);
        }
        return $binaryIp;
    }
}
