<?php

namespace Kalnoy\LaravelCommon;

use Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Support\JsonableInterface;
use Illuminate\Support\ArrayableInterface;

/**
 * Base controller.
 */
class BaseController extends Controller {

    const OK = 'ok';

    const FAIL = 'fail';

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

    /**
     * Response json data with status.
     *
     * @param mixed  $data
     * @param string $status
     * @param int    $code
     *
     * @return Response
     */
    public function responseJson($data, $status = self::OK, $code = 200)
    {
        if ($data instanceof JsonableInterface)
        {
            $data = $data->toJson();
        }
        else if ($data instanceof ArrayableInterface)
        {
            $data = $data->toArray();
        }
        else
        {
            $data = (string)$data;
        }

        return Response::json(compact('data', 'status'), $code);
    }

}