/* Banner Component
===================================================================================================================== */

import $ from 'jquery';

$(function() {
  
  // Handle Animated Banner Components:
  
  $('.bannercomponent.animated').each(function() {
    
    // Initialise:
    
    var $self    = $(this);
    var $content = $self.find('.content');
    var $wrapper = $self.find('.wrapper');
    var $slides  = $wrapper.find('.slide');
    
    // Calculate Width:
    
    var width = 0;
    
    $slides.each(function() {
      width += $(this).width();
    });
    
    // Define Content Width:
    
    $content.width(width);
    
    // Duplicate Slides:
    
    $slides.each(function() {
      $wrapper.append($(this).clone());
    });
    
  });
  
});
