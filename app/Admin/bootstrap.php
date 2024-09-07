<?php

/**
 * Open-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * OpenAdmin\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * OpenAdmin\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use OpenAdmin\Admin\Facades\Admin;

OpenAdmin\Admin\Form::forget(['editor']);
function is($role)
{
    if (!Admin::user()) {
        return false;
    }
    if(Admin::user()->isAdministrator()){
        return true;
    }

    return Admin::user()->isRole($role);
}

function isAdmin()
{
    return  is('admin');
}

function isVSP()
{
    return is('vsp');
}
