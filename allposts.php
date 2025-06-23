<?php

// This outputs the text of all published wordpress posts on a single page. Useful for, for example, submitting your writing style to train an LLM

// Fill in things you don't want displayed in the remove_shortcodes and remove_tags functions. 

// Load WordPress
// fill in the path to wp-config.php and wp-load.php in your site's root directory here. (I run it in a subdirectory of the sit root.) 
require_once('../wp-config.php'); 
require_once('../wp-load.php');

// Function to remove specific shortcodes from content
function remove_shortcodes($content) {
    // CUSTOMIZE THESE TO REMOVE SHORTCODES THAT PRODUCE NON-TEXT OR IRRELEVANT CONTENT

    // Remove gallery and photonic shortcodes (including any attributes)
    $content = preg_replace('/\[.*gallery [^\]]*\]/i', '', $content);
    $content = preg_replace('/\[.*photonic [^\]]*\]/i', '', $content);

    // Remove my "fromblog" shortcode
    $content = preg_replace('/\[.*fromblog [^\]]*\]/i', '', $content);
    
    // Remove playlist shortcodes (including any attributes)
    $content = preg_replace('/\[playlist[^\]]*\]/i', '', $content);
  
    return $content;
}

function remove_tags($content) {
// CUSTOMIZE THESE TO REMOVE TAGS THAT PRODUCE NON-TEXT OR IRRELEVANT CONTENT
    
// Remove <code>...</code> blocks
    $content = preg_replace('/<code[^>]*>.*?<\/code>/is', '', $content);

// Remove <figure> blocks
    $content = preg_replace('/<figure[^>]*>.*?<\/figure>/is', '', $content);

// Remove <iframe> blocks
    $content = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $content);

// Remove <video> blocks
    $content = preg_replace('/<video[^>]*>.*?<\/video>/is', '', $content);

// Remove <!-- --> comments
    $content = preg_replace('/<!--.*?-->/is', '', $content);

// Remove <image> tage
    $content = preg_replace('/<img[^>]*>/i', '', $content);

// replace <details> with divs so they open
    $content = preg_replace('/<(\/?)details/i', '<\1div', $content);
// replace <summary> 
    $content = preg_replace('/<(\/?)summary/i', '<\1h5', $content);

// remove embed github script
    $content = preg_replace('/<script src="https\:\/\/michaelkupietz\.com\/emgithub\/embed-v2b-mk\.js.*?<\/script>/is', '', $content);
    
// remove edit front-end 'edit' links in posts
    $content = preg_replace('/<a [^>]*href="\/wp-admin\/post\.php[^>]*>.*?<\/a>/is', '', $content);

    return $content;
}

// Set up the query to get all published posts
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => -1, // Get all posts
    'orderby' => 'date',
    'order' => 'DESC'
);

$posts = get_posts($args);

// Start output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Published Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .post-separator {
            border-top: 2px solid #333;
            margin: 40px 0 20px 0;
            padding-top: 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .post-content {
            margin-bottom: 40px;
        }
        .post-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php
if ($posts) {
    foreach ($posts as $post) {
        // Set up post data for WordPress functions
        setup_postdata($post);
        
        // Display post separator with title
        echo '<div class="post-separator">-----<br>Post name: ' . get_the_title() . '</div>';
        
        // Display post meta information
        # nah, keep this as an example but I don't need it for my purposes
        #echo '<div class="post-meta">Published on: ' . get_the_date() . '</div>';
    
        // Get the post content and remove gallery/playlist shortcodes
        $content = get_the_content();
        $content = remove_shortcodes($content);
        
        // Display the post content with remaining shortcodes processed
        echo '<div class="post-content">';
        echo remove_tags(apply_filters('the_content', $content));
        echo '</div>';
    
    }
    
    // Clean up post data
    wp_reset_postdata();
} else {
    echo '<p>No published posts found.</p>';
}
?>

</body>
</html>
