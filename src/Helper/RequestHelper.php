<?php

namespace HyperfAdminCore\Helper;

class RequestHelper
{
    /**
     * @param $agent
     * @return string
     */
    public static function os($agent): string
    {
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 11.0/i', $agent)) {
            return 'Windows 11';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }
        if (stripos($agent, 'linux') !== false) {
            return 'Linux';
        }
        if (stripos($agent, 'mac') !== false) {
            return 'Mac';
        }
        return 'Unknown';
    }

    /**
     * @param $agent
     * @return string
     */
    public static function browser($agent): string
    {
        if (stripos($agent, 'MSIE') !== false) {
            return 'MSIE';
        }
        if (stripos($agent, 'Edg') !== false) {
            return 'Edge';
        }
        if (stripos($agent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (stripos($agent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (stripos($agent, 'Safari') !== false) {
            return 'Safari';
        }
        if (stripos($agent, 'Opera') !== false) {
            return 'Opera';
        }
        return 'Unknown';
    }

    public static function ipToRegion($ip): string
    {
        return (new Ip2region(logger()))->search($ip);
    }
}