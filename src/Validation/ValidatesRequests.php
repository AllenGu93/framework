<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2016, notadd.com
 * @datetime 2016-10-21 14:37
 */
namespace Notadd\Foundation\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\ValidationException;

/**
 * Class ValidatesRequests.
 */
trait ValidatesRequests
{
    /**
     * @var string
     */
    protected $validatesRequestErrorBag;

    /**
     * Run the validation routine against the given validator.
     *
     * @param \Illuminate\Contracts\Validation\Validator|array $validator
     * @param \Illuminate\Http\Request|null                    $request
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWith($validator, Request $request = null)
    {
        $request = $request ?: app('request');
        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $rules
     * @param array                    $messages
     * @param array                    $customAttributes
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param string                   $errorBag
     * @param \Illuminate\Http\Request $request
     * @param array                    $rules
     * @param array                    $messages
     * @param array                    $customAttributes
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    public function validateWithBag(
        $errorBag,
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $this->withErrorBag($errorBag, function () use ($request, $rules, $messages, $customAttributes) {
            $this->validate($request, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Throw the failed validation exception.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationException($validator,
            $this->buildFailedValidationResponse($request, $this->formatValidationErrors($validator)));
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param \Illuminate\Http\Request $request
     * @param array                    $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return new JsonResponse($errors, 422);
        }

        return redirect()->to($this->getRedirectUrl())->withInput($request->input())->withErrors($errors,
            $this->errorBag());
    }

    /**
     * Format the validation errors to be returned.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return array
     */
    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->getMessages();
    }

    /**
     * Get the URL we should redirect to.
     *
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getRedirectUrl()
    {
        return app(UrlGenerator::class)->previous();
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }

    /**
     * Execute a Closure within with a given error bag set as the default bag.
     *
     * @param string   $errorBag
     * @param callable $callback
     *
     * @return void
     */
    protected function withErrorBag($errorBag, callable $callback)
    {
        $this->validatesRequestErrorBag = $errorBag;
        call_user_func($callback);
        $this->validatesRequestErrorBag = null;
    }

    /**
     * Get the key to be used for the view error bag.
     *
     * @return string
     */
    protected function errorBag()
    {
        return $this->validatesRequestErrorBag ?: 'default';
    }
}
