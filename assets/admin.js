/**
 * ADJM MCP Complete - Admin JavaScript
 */

(function ($) {
    'use strict';

    const AdjmMcp = {
        init: function () {
            this.bindEvents();
        },

        bindEvents: function () {
            // Toggle ability card visual
            $(document).on('change', '.adjm-ability-card input[type="checkbox"]', function () {
                const card = $(this).closest('.adjm-ability-card');
                if ($(this).is(':checked')) {
                    card.addClass('enabled');
                } else {
                    card.removeClass('enabled');
                }
            });

            // Test ability
            $(document).on('click', '.adjm-test-ability', function (e) {
                e.preventDefault();
                const ability = $(this).data('ability');
                AdjmMcp.testAbility(ability);
            });

            // Copy code blocks
            $(document).on('click', '.adjm-code-block', function () {
                const code = $(this).find('code').text();
                navigator.clipboard.writeText(code).then(function () {
                    AdjmMcp.showNotice('Code copié !', 'success');
                });
            });
        },

        testAbility: function (abilityName) {
            const params = prompt('Entrez les paramètres JSON:', '{}');
            if (params === null) return;

            $.ajax({
                url: adjmMcp.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'adjm_test_ability',
                    nonce: adjmMcp.nonce,
                    ability: abilityName,
                    params: params
                },
                beforeSend: function () {
                    AdjmMcp.showNotice('Test en cours...', 'info');
                },
                success: function (response) {
                    if (response.success) {
                        console.log('Result:', response.data.result);
                        AdjmMcp.showNotice('Ability exécutée avec succès ! Résultat dans la console.', 'success');
                    } else {
                        AdjmMcp.showNotice('Erreur: ' + response.data.message, 'error');
                    }
                },
                error: function () {
                    AdjmMcp.showNotice('Erreur de connexion', 'error');
                }
            });
        },

        showNotice: function (message, type) {
            // Remove existing notices
            $('.adjm-notice').remove();

            const notice = $('<div class="adjm-notice notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.adjm-mcp-wrap h1').after(notice);

            // Auto-dismiss after 5 seconds
            setTimeout(function () {
                notice.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    $(document).ready(function () {
        AdjmMcp.init();
    });

})(jQuery);
