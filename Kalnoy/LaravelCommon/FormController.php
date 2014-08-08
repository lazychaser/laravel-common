<?php

namespace Kalnoy\LaravelCommon;

use Redirect;
use Request;
use Input;
use View;
use Session;
use Illuminate\Support\MessageBag;
use Kalnoy\LaravelCommon\Service\Form\FormInterface;
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
     * Init controller.
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Process the form.
     */
    public function process()
    {
        return $this->processForm($this->getInput())
            ? (Request::ajax() ? $this->handleAjaxSuccess() : $this->handleSuccess())
            : (Request::ajax() ? $this->handleAjaxFailure() : $this->handleFailure());
    }

    /**
     * Process the form with the input.
     * 
     * @param array $input
     * 
     * @return bool
     */
    protected function processForm($input)
    {
        if ($this->form->process($input))
        {
            $this->dispatchEvents();

            return true;
        }

        return false;
    }

    /**
     * Process form with the input and action.
     * 
     * @param array $input
     * @param string $action
     * 
     * @return bool
     */
    protected function processAction(array $input, $action)
    {
        $input['action'] = $action;

        return $this->processForm($input);
    }

    /**
     * Get an input.
     * 
     * @return array
     */
    protected function getInput()
    {
        if ($id = $this->form->id()) return Input::get($id, []);

        return Input::all();
    }

    /**
     * Dispatch form events.
     */
    protected function dispatchEvents()
    {
        $this->getDispatcher()->dispatch($this->form);
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

        $this->flashMessage();

        Session::ageFlashData();

        return $this->responseJson($view, self::FAIL);
    }

    /**
     * Get a response when form processing succeeded for ajax request.
     */
    protected function handleAjaxSuccess()
    {
        if ( ! $this->formView) return $this->responseJSON($this->form->getAlert());

        // We consider that request input is processed so we should clear it
        // before rendering a view
        app('request')->request->replace();

        $this->flashMessage();

        Session::ageFlashData();

        return $this->responseJson($this->formView());
    }

    /**
     * Make a form view.
     */
    protected function formView(array $data = array())
    {
        return View::make($this->formView, $data)->with('form', $this->form);
    }

    /**
     * Handle a succcessfull form processing and return some response.
     */
    protected function handleSuccess()
    {
        $this->flashMessage();

        return Redirect::back();
    }

    /**
     * Handle a failed form processing and return some response.
     */
    protected function handleFailure()
    {
        $this->flashMessage();

        return Redirect::back()->withInput()->withErrors($this->form->errors());
    }

    /**
     * Flash form's message if any.
     */
    protected function flashMessage()
    {
        if ($message = $this->form->message()) $message->flash();
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