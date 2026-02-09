<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Markdown extends Component
{
    /**
     * Create a new component instance.
     */

    public string $html;
    public function __construct(public ?string $text = '')
    {
        //
        $rawHtml = Str::markdown($text ?? '');
        $this->html = clean($rawHtml); // from mews/purifier
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.markdown');
    }
}
