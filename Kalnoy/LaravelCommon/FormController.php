<?php

namespace Kalnoy\LaravelCommon;

use Redirect;
use Request;
use Input;
use View;
use Session;
use Illuminate\Support\MessageBag;
use Kalnoy\LaravelCommon\Service\Form\BasicForm;
use Kalnoy\LaravelCommon\Events\Dispatcher;

/**
 * A base controller for handling forms.
 * 
 * It will do redirects for usual requests and will render a form for ajax requests.
 */
abstract class FormController extends BaseController {

    /**
     * Event dispatcher.
     * 
     * @var \Kalnoy\LaravelCommon\Events\Dispatcher
     */
    private $dispatcher;

    /**
     * The form processor.
     */
    protected $form;

    /**
     * A form view.
     * 
     * @var string
     */
    protected $formView;

    /**
     * The message that will be shown when form succeeded processing.
     * 
     * @var string
     */
    protected $successMessage;

    /**
     * The message that will be shown when form processing has failed.
     * 
     * @var string
     */
    protected $failureMessage;

    /**
     * A domain for alerts.
     * 
     * @var string
     */
    protected $alertDomain;

    /**
     * Alert type for success.
     *
     * @var string
     */
    protected $successAlert = 'success';

    /**
     * Alert type for failure.
     * 
     * @var string
     */
    protected $failureAlert = 'warning';

    /**
     * Init controller.
     */
    public function __construct(BasicForm $form)
    {
        $this->form = $form;
    }

    /**
     * Process the form.
     */
    public function process()
    {
        if ($this->form->process(Input::all()))
        {
            // Dispatch form events.
            $this->getDispatcher()->dispatch($this->form);

            return Request::ajax() ? $this->handleAjaxSuccess() : $this->handleSuccess();
        }

        return Request::ajax() ? $this->handleAjaxFailure() : $this->handleFailure();
    }

    /**
     * Get a response when form processing failed for ajax request.
     */
    protected function handleAjaxFailure()
    {
        $errors = new MessageBag($this->form->errors());

        if ( ! $this->formView) return $this->responseJSON(compact('errors'), self::FAIL);

        $view = $this->formView();

        // We'll provide an errors for the view as well as session, too
        $view->withErrors($errors);
        Session::flash('errors', $errors);

        // Flash the input so that form could access it
        Input::flash();

        if ($this->failureMessage)
        {
            Session::flash($this->getAlertId($this->failureAlert), $this->failureMessage);
        }

        Session::ageFlashData();

        return $this->responseJSON($view, self::FAIL);
    }

    /**
     * Get a response when form processing succeeded for ajax request.
     */
    protected function handleAjaxSuccess()
    {
        if ( ! $this->formView) return $this->responseJSON($this->successMessage);

        // We consider that request input is processed so we should clear it
        // before rendering a view
        app('request')->request->replace();

        if ($this->successMessage)
        {
            Session::flash($this->getAlertId($this->successAlert), $this->successMessage);
        }

        Session::ageFlashData();

        return $this->responseJSON($this->formView());
    }

    /**
     * Make a form view.
     */
    protected function formView()
    {
        return View::make($this->formView);
    }

    /**
     * Handle a succcessfull form processing and return some response.
     */
    protected function handleSuccess()
    {
        $redirect = $this->successRedirect();

        if ($this->successMessage)
        {
            $redirect->with($this->getAlertId($this->successAlert), $this->successMessage);
        }

        return $redirect;
    }

    /**
     * Get a redirect when processing succeeded.
     */
    protected function successRedirect()
    {
        return Redirect::back();
    }

    /**
     * Handle a failed form processing and return some response.
     */
    protected function handleFailure()
    {
        $redirect = Redirect::back()->withInput();
            
        $redirect->withErrors($this->form->errors());

        if ($this->failureMessage)
        {
            $redirect->with($this->getAlertId($this->failureAlert), $this->failureMessage);
        }

        return $redirect;
    }

    /**
     * Get alert id.
     *
     * @param string $alert
     *
     * @return string
     */
    public function getAlertId($alert)
    {
        if ($this->alertDomain) $alert = $this->alertDomain.'.'.$alert;

        return $alert;
    }

    /**
     * Get event dispatcher.
     * 
     * @return \Kalnoy\LaravelCommon\Events\Dispatcher
     */
    public function getDispatcher()
    {
        if ($this->dispatcher === null)
        {
            return $this->dispatcher = app('Kalnoy\LaravelCommon\Events\Dispatcher');
        }

        return $this->dispatcher;
    }

    /**
     * Set event dispatcher object.
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

}