<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait RolifiesView
{
    /**
     * Returns a view based on the role of the logged in user
     *
     * @param  string $view
     * @return \View
     */
    protected function rolifiedView($view)
    {
        // FIXME: If supporting more than one role per user, this will need to be rethought.
        $appendix = '';

        if (\Auth::check()) {
            $role = \Auth::user()->roles()->first()->slug;
            $appendix = '-by-' . $role;
        }

        return \View::exists($view . $appendix) ? view($view . $appendix) : view($view);
    }
}
