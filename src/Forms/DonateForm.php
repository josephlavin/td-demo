<?php

namespace Drupal\donation_page\Forms;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DonateForm extends FormBase
{
    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId()
    {
        return 'donate_page';
    }

    /**
     * Form constructor.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   The form structure.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attributes'] = ['id' => 'donation-form'];
        $form['js'] = [
            '#attached' => [
                'library' => [
                    'donation_page/custom_checkout',
                ],
            ]
        ];

        $form['price_select'] = array(
            '#markup' => '<price-select></price-select>',
            '#allowed_tags' => ['price-select']
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        // Validation is optional.
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }
}