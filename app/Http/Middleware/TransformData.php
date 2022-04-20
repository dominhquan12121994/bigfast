<?php
namespace App\Http\Middleware;

use Closure;

use Symfony\Component\HttpFoundation\ParameterBag;

class TransformData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isJson()) {
            $this->clean($request->json());
        } else {
            $this->clean($request->request);
        }

        return $next($request);
    }

    /**
     * Clean the request's data by removing mask from phonenumber.
     *
     * @param  \Symfony\Component\HttpFoundation\ParameterBag  $bag
     * @return void
     */
    private function clean(ParameterBag $bag)
    {
        $bag->replace($this->cleanData($bag->all()));
    }

    /**
     * Check the parameters and clean the number
     *
     * @param  array  $data
     * @return array
     */
    private function cleanData(array $data)
    {
        $numberParams = array();

        $stringParams = array(
            'email', 'phone', 'password'
        );

        $regexNumber = array(
            'receiverPhone', 'phone'
        );

        return collect($data)->map(function ($value, $key) use ($numberParams, $stringParams, $regexNumber) {

            if (in_array($key, $stringParams)) {
                return $value;
            }

            if (in_array($key, $regexNumber)) {
                return preg_replace("/([^0-9])/", '', $value);
            }

            if (is_numeric($value) && strlen($value) < 10) {
                return (int)$value;
            }

            return $value;
        })->all();
    }
}