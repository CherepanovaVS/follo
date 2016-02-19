<?php

/**
 * @file
 * Contains \Drupal\follow\Plugin\Block\User.
 */

namespace Drupal\follow\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


/**
 * Provides the User block.
 *
 * @Block(
 *   id = "user",
 *   admin_label = @Translation("Follow User")
 * )
 */
class User extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $args = explode('/', Url::fromRoute('<current>'));
    $uid = $args[1];
    if ($args[0] == 'user' && is_numeric($uid) && ($content = _follow_block_content('user', $uid))) {
      return array(
        'subject' => _follow_block_subject($uid),
        'content' => $content,
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['follow_title'] = array(
      '#type' => 'radios',
      '#title' => t('Default block title'),
      '#default_value' => \Drupal::config('follow.settings')->get('follow_user_block_title'),
      '#options' => array(
        FOLLOW_NAME => t('Follow [username] on'),
        FOLLOW_ME => t('Follow me on'),
      ),
    );
    $form['follow_alignment'] = array(
      '#type' => 'select',
      '#title' => t('Alignment'),
      '#options' => array(
        'vertical' => t('Vertical'),
        'horizontal' => t('Horizontal'),
      ),
      '#description' => t('Whether the icons are to appear horizontally beside each other, or one after another in a list.'),
      '#default_value' => \Drupal::config('follow.settings')->get("follow_user_alignment"),
    );
    // Allow changing which icon style to use on the global service links.
    $form['follow_icon_style'] = array(
      '#type' => 'select',
      '#title' => t('Icon Style'),
      '#options' => follow_icon_style_options(),
      '#description' => t('How the Follow icons should appear.'),
      '#default_value' => \Drupal::config('follow.settings')->get('follow_user_icon_style'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('follow.settings');
    $values = $form_state->getValues();
    $config->set('follow_user_block_title', $values['follow_title'])->save();
    $config->set('follow_user_alignment', $values['follow_alignment'])->save();
    $config->set('follow_user_icon_style', $values['follow_icon_style'])->save();
    // Reset the CSS in case the styles changed.
    follow_save_css(TRUE);
  }

}
