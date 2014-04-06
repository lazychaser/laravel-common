<?php

namespace Kalnoy\LaravelCommon;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Base controller.
 */
class BaseController extends Controller {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout))
        {
            $this->layout = \View::make($this->layout);
        }
    }

    /**
     * Set layout meta info.
     *
     * @param string $title
     * @param string $metaTitle
     * @param string $keywords
     * @param string $description
     */
    public function setMeta($title, $keywords = null, $description = null)
    {
        if ($title instanceof MetaHolderInterface) return $this->setMetaFromModel($title);

        if ( $this->layout instanceof View )
        {
            $this->layout->title = $title;
            $this->layout->keywords = $keywords;
            $this->layout->description = $description;
        }

        return $this;
    }

    /**
     * Set meta from a meta holder.
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Model $meta
     */
    public function setMetaFromModel($model)
    {
        return $this->setMeta($model->getMetaTitle(), $model->getMetaKeywords(), $model->getMetaDescription());
    }

}