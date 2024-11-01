<?php 

/**
 * $settings is an array and contains following indexes
    array(
        'number_of_tweets',
        'skip_keywords',
        'skip_replies',
        'skip_rts',
        'theme',
        'title',
        'title_icon',
        'title_link',
        'profile_link',
        'follow_button',
        'owner_thumbnail',
        'others_thumbnail',
        'links_clickable',
        'time',
        'time_format',
        'time_linked',
        'screen_name'
    )
 */

global $settings, $before_title, $after_title; ?>

<div class="twp-widget-header">

    <?php echo $before_title; ?>
       
        <?php if( $settings['title_link'] ) : ?>
            <a href="https://twitter.com/<?php echo $settings['screen_name']; ?>" target="_blank" title="Follow <?php echo $settings['screen_name']; ?> on Twitter">
        <?php endif; ?>
        
        <?php if( $settings['title_icon'] ) : ?>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAFKUlEQVR4Xu1aXVrqSBA9J3CfR3QB46zgMiuQuwLxM5lXuSsQVzDMCuSu4OLrJI7MCgZ3oCuQuwCNPgNd83UQjJCfTggRrubJT6qrq06frqqubuKdf3zn/uMDgA8GvHMEPrbAJhOgeeXvVCY4tCBNAXYINJ7tHQowhMgNxbpw/6jd5PVjIxmgHf80UecAWyaOCfAoYPvSrl2kyTev/P1PE9UeVaxO/6j2uATAsec3L+1aP03Run53Lv2WiJwT2Mk8h+AGwq9RjLBdv0GqEw2qQC48ey8A9xUAU3TkDuRX97jWy2zAigNs9/6cZHsVNZoNVPyiQXD+8Q9kopokmwD2A72Cp1GV9f5RbbgEgOPddwGeBoIlg2C79x2Sf67i/GxsAEIMg0R45jm17kz2FQMc7+FujlSJIGjaQ+R7Ec4n6QhTfwmAaeARf0nBmpmg561O5C7Xns+A2KLztusf0kJtzoBpkJD/onSKSNdz9s4yzGcsWiT14ybVtKdgoCx8tqAaAjYp4KjKfSMAprEDg3GFRzp1GHtnIGh7D/6aV18Hu2kADH+Kv+tAaQzAMwiPEB55Tm1g4FuqSBLrUgevIhDa1pkAeJlTeqOKdbYqG8qgf1pMmwMQGwRjkJ6mGnZcu/Yt72LY7kOfxGHe8ZnGCZ4U2Vos8l6lQdt7GBL4NZNiXZOD3VEFF1kZ4bgPAxAHGefLI34LxVZUhbhQB4QKoYzTaEaA7I6tAIigykr7ygJARP7ynL1OlD3RpXCa5Wm/65qc7EHhOumktnEAaL9elcNpjhr8HjADASADKNywgscRcau3S9FzxZljzACtIAiGYxmC+MXAv60QWaz/w0ZH9gOcv/06KDpA/RQgiPBLXO0yB0D3ASh4nAn+TCAYARCqyoYi0gesAYl9gZzlSI0btTVGFdbiUnTOSnCj/Es1xrV3Y1t/uSvB1Fk3RUBw7Tq7s2bqcmUc/k9ZeblcbOSba+/FttleV4IldWbKBECBR0lN3qW9kfM8UKZPmeYaVfhbUmke2Ra3IFeZZtlc4VvX3q0nmRcZHW3vvkfwZHP9MrMsqQKcaYhND6We1c38ySyVRn+tMBaAoFs7ll5pDYvM7qUMSEl/qQyYCQQ9eyXdbTsXpEV/YwBmJ8TqWLVBtrahLBbgh2fvLneCI0iTeDusD0iWhflliVLY11fV+tRcNGML1ZfhMicRANv126ScF2rcmpVlWf3EIDij/tY1RzKsfioAWmCrWGAY+cMkNHohshU1wcK9v+lOMwLg+dJEX4d9NlVctpxJ1RdlkxEA83gwkY0EQQT/es5ursxkDMAMvTe5z0um0+2owkbWW6lMhdDi/LOXVhC23rRCFDxB2HjTZ3K6mQqoacuJPF3zXf/LWhTgvFEaNAlmmhHViXwPPWQ0GZZfpiDnCwHA8fxTgXS2beVXigF68LHnn1gQfeNqdOjIv9xh1uMHFZur7PlFOzJlgYDqCicQaZe24jOLBdejKpt5o33cAiQCMG2K4IBUTYA60JW22mGD8xY5JqxjEMWpWgCDlyEU7IBIbCSaKC5IJvZlR0H6X1piQYEDtt80rz97pY+0JDtlvFdefCGyozs/bwVEmY6nZoHpdbm0ymiK6mesEKtX1PvDLNsjNQs8B8IWIA0dG4roCQYrDekrWINJBYOiI3uhAESdAyoT1CmqzmlmCD4h9Lvb8BO7W0jwPkhH1hsJ/rYG4yqGpq/IsjiSVzaVAXkVb8u4DwC2ZaXWZecHA9aF7LboffcM+B+f2F+uoqujygAAAABJRU5ErkJggg=="/>
        <?php endif; ?>
        
            <?php echo $settings['title']; ?>
        
        <?php if( $settings['title_link'] ) : ?>
            </a>
        <?php endif; ?>
        
    <?php echo $after_title; ?>
    
</div>