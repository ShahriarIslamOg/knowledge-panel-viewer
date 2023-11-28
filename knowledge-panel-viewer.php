<?php
/*
Plugin Name: Knowledge Panel Viewer
Description: Display Knowledge Panel information using a shortcode.
Version: 1.0
Author: Shahriar Islam
*/

// Shortcode function
function knowledge_panel_shortcode() {
    ob_start(); // Start output buffering

    // Function to fetch Knowledge Panel information and display results
    function getKnowledgePanelList($query, $limit) {
        $api_key = "YOUR_GOOGLE_KNOWLEDGE_GRAPH_API_KEY";
        $formatted_query = str_replace(' ', '+', $query);
        $url = "https://kgsearch.googleapis.com/v1/entities:search?query=" . urlencode($formatted_query) . "&key=" . $api_key . "&limit=$limit&indent=True";

        // Fetch JSON response
        $json_response = file_get_contents($url);
        $data = json_decode($json_response, true);

echo'<form method="post" action="">
        <label for="query">Enter a query:</label>
        <input type="text" id="query" name="query" required>
        <label for="limit">Enter result limit:</label>
        <input type="number" id="limit" name="limit" min="1" required><br/><br/>
        <input type="submit" value="Search">
    </form><br/><br/>';

        // Check if results are present
        if (isset($data['itemListElement']) && count($data['itemListElement']) > 0) {
            echo "<ul>";
            foreach ($data['itemListElement'] as $result) {
                // Extract KGID from response
                $kg_id = str_replace('kg:', '', $result['result']['@id']);

                // Build Knowledge Panel link
                $knowledge_panel_link = "https://www.google.com/search?kgmid=" . $kg_id;

                // Display result
                echo "<li><strong>Name:</strong> {$result['result']['name']} | <strong>Knowledge Panel Link:</strong> <a href='$knowledge_panel_link' target='_blank'>$knowledge_panel_link</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No results found for the given query.</p>";
        }
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_input = $_POST["query"];
        $user_limit = $_POST["limit"];
        getKnowledgePanelList($user_input, $user_limit);
    }

    // User input form
    ?>


    <?php

    return ob_get_clean(); // Return the buffered content
}

// Register the shortcode
add_shortcode('knowledge_panel_viewer', 'knowledge_panel_shortcode');

// Add instructions menu in the dashboard
function knowledge_panel_add_menu() {
    add_menu_page(
        'Knowledge Panel Viewer Instructions',
        'Knowledge Panel Viewer',
        'manage_options',
        'knowledge_panel_instructions',
        'knowledge_panel_instructions_page'
    );
}

add_action('admin_menu', 'knowledge_panel_add_menu');

// Instructions page content
function knowledge_panel_instructions_page() {
    ?>
    <div class="wrap">
        <h1>Knowledge Panel Viewer Instructions</h1>
        <p>Use the shortcode <code>[knowledge_panel_viewer]</code> in any WordPress post or page to display the Knowledge Panel Viewer form.</p>
        <p>Fill in the query and result limit, then click the "Search" button to view Knowledge Panel information.</p>
        <p>For more details, refer to the documentation.</p>
    </div>
    <?php
}
?>