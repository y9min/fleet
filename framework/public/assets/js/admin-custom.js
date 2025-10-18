
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

    // Form validation enhancement
    $('form').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        $submitBtn.prop('disabled', true)
                  .html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        // Re-enable after 3 seconds (adjust as needed)
        setTimeout(function() {
            $submitBtn.prop('disabled', false)
                      .html($submitBtn.data('original-text') || 'Submit');
        }, 3000);
    });

    // Store original button text
    $('button[type="submit"]').each(function() {
        $(this).data('original-text', $(this).html());
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
