<?php

namespace Zipferot3000\LaravelFilepondAdapter;

if (!function_exists('fp_adapter')) {
    function fp_adapter() {
        return new FPAdapter();
    }
}