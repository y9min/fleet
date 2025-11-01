
// Admin Dashboard Custom JavaScript

$(document).ready(function() {
    // Add loading states to buttons
    $('.btn').on('click', function() {
        var $btn = $(this);
        if ($btn.hasClass('btn-loading')) return false;
        
        $btn.addClass('btn-loading');
        var originalText = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        
        setTimeout(function() {
            $btn.removeClass('btn-loading').html(originalText);
        }, 2000);
    });

    // Smooth scrolling for sidebar navigation
    $('.sidebar-menu a').on('click', function(e) {
        var target = $(this);
        if (target.attr('href').indexOf('#') === 0) {
            e.preventDefault();
            var targetElement = $(target.attr('href'));
            if (targetElement.length) {
                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 70
                }, 500);
            }
        }
    });

    // Add hover effects to cards
    $('.card, .box').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );

    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut('slow');

    // Add ripple effect to buttons
    $('.btn').on('click', function(e) {
        var $btn = $(this);
        var $ripple = $('<span class="ripple"></span>');
        
        $btn.append($ripple);
        
        var btnOffset = $btn.offset();
        var xPos = e.pageX - btnOffset.left;
        var yPos = e.pageY - btnOffset.top;
        
        $ripple.css({
            width: '20px',
            height: '20px',
            top: yPos - 10,
            left: xPos - 10
        }).addClass('ripple-effect');
        
        setTimeout(function() {
            $ripple.remove();
        }, 600);
    });

    // Sidebar toggle enhancement
    $('[data-widget="pushmenu"]').on('click', function() {
        setTimeout(function() {
            $(window).trigger('resize');
        }, 300);
    });

    // Add tooltips to navigation items
    $('[data-toggle="tooltip"]').tooltip();

    // Form validation enhancement with proper loading states
    // Only add loading indicator if form doesn't use easyAjax (which handles it automatically)
    $('form').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
        
        // Check if form uses easyAjax (which already handles loading states)
        if ($form.data('easy-ajax') || $form.hasClass('easy-ajax')) {
            return; // Let easyAjax handle loading states
        }
        
        // Only add loading indicator for non-AJAX forms or as fallback
        if ($submitBtn.length > 0) {
            if (!$submitBtn.data('original-text')) {
                if ($submitBtn.is('input')) {
                    $submitBtn.data('original-text', $submitBtn.val());
                } else {
                    $submitBtn.data('original-text', $submitBtn.html());
                }
            }
            
            var loadingText = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            
            if ($submitBtn.is('input')) {
                $submitBtn.prop('disabled', true).val('Processing...');
            } else {
                $submitBtn.prop('disabled', true).html(loadingText).addClass('btn-loading');
            }
            
            // Re-enable after 30 seconds as safety fallback (for non-AJAX forms)
            setTimeout(function() {
                if ($submitBtn.prop('disabled')) {
                    if ($submitBtn.is('input')) {
                        $submitBtn.val($submitBtn.data('original-text') || 'Submit');
                    } else {
                        $submitBtn.html($submitBtn.data('original-text') || 'Submit');
                    }
                    $submitBtn.prop('disabled', false).removeClass('btn-loading');
                }
            }, 30000);
        }
    });

    // Enhanced delete operations with loading indicators
    // This handler runs early to add loading states, but allows existing handlers to proceed
    $(document).on('click', 'button[class*="delete"], button[data-action*="delete"], a[class*="delete"]', function(e) {
        var $button = $(this);
        
        // Skip if already processing or if it's a confirmation modal button
        if ($button.hasClass('deleting') || $button.closest('.modal').length > 0) {
            return;
        }
        
        // Only add loading state for actual delete buttons (not cancel buttons)
        if ($button.text().toLowerCase().indexOf('delete') === -1 && 
            $button.attr('href') && $button.attr('href').indexOf('delete') === -1) {
            return;
        }
        
        // Store original button state
        if (!$button.data('original-html')) {
            $button.data('original-html', $button.html());
        }
        
        // Show loading state immediately
        $button.addClass('deleting')
               .prop('disabled', true);
        
        var loadingHtml = '<i class="fa fa-spinner fa-spin"></i> ';
        if (!$button.is('a')) {
            loadingHtml += 'Deleting...';
            $button.html(loadingHtml);
        }
        
        // Optimistic UI: Fade out row if it exists
        var $row = $button.closest('tr');
        if ($row.length > 0 && !$row.hasClass('deleting-row')) {
            $row.addClass('deleting-row');
            $row.css({
                'opacity': '0.6',
                'transition': 'opacity 0.2s'
            });
        }
        
        // Track delete action if tracking is available
        if (window.trackAction) {
            var itemId = $button.data('id') || $row.find('input[type="checkbox"]').val() || 'item';
            window.trackAction('delete', itemId);
        }
        
        // Restore button state after 10 seconds if delete hasn't completed
        // (existing delete handlers should handle the actual deletion)
        setTimeout(function() {
            if ($button.hasClass('deleting') && $button.prop('disabled')) {
                var originalHtml = $button.data('original-html');
                if (originalHtml) {
                    $button.html(originalHtml);
                }
                $button.prop('disabled', false).removeClass('deleting');
                if ($row.length > 0) {
                    $row.css('opacity', '1').removeClass('deleting-row');
                }
            }
        }, 10000);
    });
    
    // Enhanced dropdown behavior
    $('.dropdown-toggle').on('shown.bs.dropdown', function () {
        $(this).closest('.dropdown').addClass('open');
    });
    
    $('.dropdown-toggle').on('hidden.bs.dropdown', function () {
        $(this).closest('.dropdown').removeClass('open');
    });

    // Add search functionality to sidebar (if search input exists)
    $('#sidebar-search').on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase();
        $('.sidebar-menu li').each(function() {
            var $li = $(this);
            var text = $li.find('a').text().toLowerCase();
            
            if (text.indexOf(searchTerm) === -1 && !$li.hasClass('header')) {
                $li.hide();
            } else {
                $li.show();
            }
        });
    });

    // Progress bar animation
    $('.progress-bar').each(function() {
        var $bar = $(this);
        var width = $bar.data('width') || $bar.attr('aria-valuenow');
        
        $bar.css('width', '0%');
        setTimeout(function() {
            $bar.animate({
                width: width + '%'
            }, 1000);
        }, 500);
    });

    // Add confirmation dialogs for delete actions
    $('a[href*="delete"], button[data-action="delete"]').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        
        if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            window.location.href = $this.attr('href') || $this.data('url');
        }
    });

    // Add success/error message styling
    function showMessage(message, type) {
        var alertClass = 'alert-' + (type || 'info');
        var $alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                      message +
                      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                      '<span aria-hidden="true">&times;</span>' +
                      '</button>' +
                      '</div>');
        
        $('.content').prepend($alert);
        
        setTimeout(function() {
            $alert.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Expose showMessage function globally
    window.showMessage = showMessage;
});

// Add custom CSS for ripple effect
$('<style>').appendTo('head').html(`
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
`);
