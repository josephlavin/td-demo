<?php

namespace Drupal\donation_page\Controllers;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DonationApiController extends ControllerBase
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function insert(Request $request)
    {
        $donation = json_decode($request->getContent(), true);

        // save the attempt to database
        $conn = Database::getConnection();
        $id = $conn->insert('donations')->fields([
            'card' => $donation['token']['card']['last4'],
            'email' => $donation['token']['email'],
            'amount' => $donation['amount']
        ])->execute();

        // Ping stripes API
        // NOTE: API KEY would normally be loaded via. secure method...
        Stripe::setApiKey('sk_test_EQ6piXnN7dZX80C8by73A3XX');

        try {
            $charge = Charge::create([
                'amount' => $donation['amount'],
                'currency' => 'usd',
                'source' => $donation['token']['id'],
                'metadata' => ['id' => $id]
            ]);
        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            return $this->handleError($e->getMessage(), $id);
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            return $this->handleError($e->getMessage(), $id);
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            return $this->handleError($e->getMessage(), $id);
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return $this->handleError($e->getMessage(), $id);
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
            return $this->handleError($e->getMessage(), $id);
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return $this->handleError($e->getMessage(), $id);
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            return $this->handleError($e->getMessage(), $id);
        }

        // update the entry
        Database::getConnection()->update('donations')->fields([
            'charged' => 1,
            'charged_id' => $charge->id
        ])->condition('id', $id)->execute();

        // return success message
        return new JsonResponse([
            'status' => 'success',
            'donation_id' => $id
        ]);
    }

    /**
     * @param $message
     * @param $donationId
     * @return JsonResponse
     */
    private function handleError($message, $donationId)
    {
        Database::getConnection()->update('donations')->fields([
            'charged' => 0,
            'charged_msg' => $message
        ])->condition('id', $donationId)->execute();

        return new JsonResponse([
            'status' => 'error',
            'msg' => $message
        ], 402);
    }
}