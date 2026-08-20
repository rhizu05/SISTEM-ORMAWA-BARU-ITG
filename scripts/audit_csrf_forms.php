<?php
/**
 * CSRF Form Audit Script
 * 
 * This script identifies all POST forms in the application
 * and generates a report of forms that need CSRF tokens.
 */

define('APP_RUNNING', true);
define('ROOT_PATH', __DIR__);

$total_forms = 0;
$forms_with_csrf = 0;
$forms_without_csrf = 0;
$forms_with_csrf_but_wrong = 0;

$report = [];

function scan_directory($dir) {
    global $total_forms, $forms_with_csrf, $forms_without_csrf, $forms_with_csrf_but_wrong, $report;
    
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            // Skip vendor directories and uploads
            if (!in_array($file, ['vendor', 'uploads', 'node_modules', '.git'])) {
                scan_directory($path);
            }
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            analyze_file($path);
        }
    }
}

function analyze_file($file_path) {
    global $total_forms, $forms_with_csrf, $forms_without_csrf, $forms_with_csrf_but_wrong, $report;
    
    $content = file_get_contents($file_path);
    $lines = explode("\n", $content);
    
    $line_number = 0;
    foreach ($lines as $line) {
        $line_number++;
        
        // Look for form tags with method="POST" or method="post" (case insensitive)
        // Handle both <form method="POST"> and <form method="post">
        if (preg_match('/<form[^>]*method\s*=\s*["\']?POST["\']?[^>]*>/i', $line) || 
            (strpos(strtolower($line), '<form') !== false && strpos(strtolower($line), 'method="post"') !== false) ||
            (strpos(strtolower($line), '<form') !== false && strpos(strtolower($line), "method='post'") !== false)) {
            
            $total_forms++;
            
            $form_start_line = $line_number;
            
            // Check if this form has CSRF token
            $has_csrf = false;
            $csrf_correct = false;
            
            // Check current line and next few lines for CSRF token
            for ($i = max(0, $line_number - 5); $i < min(count($lines), $line_number + 10); $i++) {
                if (strpos($lines[$i], 'csrf_field()') !== false || 
                    strpos($lines[$i], 'csrf_token') !== false ||
                    strpos($lines[$i], 'name="csrf_token"') !== false) {
                    $has_csrf = true;
                    
                    // Check if it's the correct format
                    if (strpos($lines[$i], '<?php echo csrf_field(); ?>') !== false ||
                        strpos($lines[$i], "<?php echo csrf_field(); ?>") !== false) {
                        $csrf_correct = true;
                    }
                    break;
                }
            }
            
            // Extract form details
            $form_action = 'unknown';
            if (preg_match('/action\s*=\s*["\']([^"\']+)["\']/', $line, $action_match)) {
                $form_action = $action_match[1];
            }
            
            // Extract page parameter from action if exists
            $page = 'unknown';
            if (preg_match('/page=([^&"\']+)/', $form_action, $page_match)) {
                $page = $page_match[1];
            }
            
            $form_info = [
                'file' => str_replace(ROOT_PATH . '/', '', $file_path),
                'line' => $form_start_line,
                'action' => $form_action,
                'page' => $page,
                'has_csrf' => $has_csrf,
                'csrf_correct' => $csrf_correct
            ];
            
            $report[] = $form_info;
            
            if ($has_csrf) {
                if ($csrf_correct) {
                    $forms_with_csrf++;
                } else {
                    $forms_with_csrf_but_wrong++;
                }
            } else {
                $forms_without_csrf++;
            }
        }
    }
}

// Start scanning
echo "========================================\n";
echo "CSRF Form Audit Report\n";
echo "Generated: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n\n";

scan_directory(ROOT_PATH);

// Generate summary
echo "SUMMARY:\n";
echo "--------\n";
echo "Total POST forms found: " . $total_forms . "\n";

if ($total_forms > 0) {
    echo "Forms with correct CSRF: " . $forms_with_csrf . " (" . round(($forms_with_csrf/$total_forms)*100, 1) . "%)\n";
    echo "Forms without CSRF: " . $forms_without_csrf . " (" . round(($forms_without_csrf/$total_forms)*100, 1) . "%)\n";
    echo "Forms with incorrect CSRF format: " . $forms_with_csrf_but_wrong . "\n\n";
} else {
    echo "No POST forms found. Please check regex pattern.\n\n";
}

// Generate detailed report
echo "DETAILED REPORT:\n";
echo "----------------\n";

foreach ($report as $index => $form) {
    $status = "❌ MISSING CSRF";
    if ($form['has_csrf']) {
        $status = $form['csrf_correct'] ? "✅ CSRF OK" : "⚠️  CSRF INCORRECT FORMAT";
    }
    
    echo ($index + 1) . ". " . $status . "\n";
    echo "   File: " . $form['file'] . " (Line " . $form['line'] . ")\n";
    echo "   Action: " . $form['action'] . "\n";
    echo "   Page: " . $form['page'] . "\n";
    
    if (!$form['has_csrf']) {
        echo "   Fix: Add <?php echo csrf_field(); ?> inside the form\n";
    } elseif (!$form['csrf_correct']) {
        echo "   Fix: Replace with <?php echo csrf_field(); ?>\n";
    }
    echo "\n";
}

// Generate fix script
echo "QUICK FIX SCRIPT:\n";
echo "-----------------\n";
echo "Copy and run this script to add CSRF tokens to all forms:\n\n";

echo "<?php\n";
echo "// fix_csrf_tokens.php\n";
echo "define('APP_RUNNING', true);\n";
echo "define('ROOT_PATH', __DIR__);\n";
echo "\n";
echo "\$forms_to_fix = [\n";

foreach ($report as $form) {
    if (!$form['has_csrf'] || !$form['csrf_correct']) {
        echo "    ['file' => '" . $form['file'] . "', 'line' => " . $form['line'] . "],\n";
    }
}

echo "];\n";
echo "\n";
echo "foreach (\$forms_to_fix as \$form) {\n";
echo "    \$file_path = ROOT_PATH . '/' . \$form['file'];\n";
echo "    \$content = file_get_contents(\$file_path);\n";
echo "    \$lines = explode(\"\\n\", \$content);\n";
echo "    \n";
echo "    // Find the form line and add CSRF token on next line\n";
echo "    \$form_line = \$form['line'] - 1; // Convert to 0-index\n";
echo "    if (isset(\$lines[\$form_line])) {\n";
echo "        // Insert CSRF token after form opening tag\n";
echo "        \$new_line = \$lines[\$form_line] . \"\\n    <?php echo csrf_field(); ?>\";\n";
echo "        \$lines[\$form_line] = \$new_line;\n";
echo "        \n";
echo "        file_put_contents(\$file_path, implode(\"\\n\", \$lines));\n";
echo "        echo \"Fixed: \" . \$form['file'] . \"\\n\";\n";
echo "    }\n";
echo "}\n";
echo "?>\n";

// Save report to file
$report_file = ROOT_PATH . '/docs/csrf_audit_report_' . date('Ymd_His') . '.txt';
file_put_contents($report_file, ob_get_contents());
echo "\nReport saved to: " . $report_file . "\n";