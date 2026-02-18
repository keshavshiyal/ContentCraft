<?php
// materials/practical.php
require_once '../includes/config.php';
require_once '../includes/Parsedown.php';
require_once '../templates/header.php';

// --- Handle Logic ---
$generatedContent = "";
$errorMsg = "";
$topic = "";
$language = "Gujarati"; // Default
$fontClass = "lang-gujarati";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic']);
    $language = $_POST['language'];

    // Determine Font Class
    switch ($language) {
        case 'Hindi':
            $fontClass = 'lang-hindi';
            break;
        case 'English':
            $fontClass = 'lang-english';
            break;
        default:
            $fontClass = 'lang-gujarati';
            break;
    }

    if (!empty($topic) && defined('GEMINI_API_KEY')) {

        // --- FIX 1: Use the correct variables ($topic, $language) inside the string ---
        // --- FIX 2: Rename variable to $promptText to match the API call below ---
        $promptText = "Act as an expert technical instructor for ITI/College students. " .
            "Generate a professional, industry-standard practical note for the experiment: \"$topic\" in $language.\n\n" .

            "**CRITICAL TERMINOLOGY RULE:**\n" .
            "- If the output language is NOT English (e.g., Gujarati or Hindi), you MUST write the **English Technical Term** in brackets immediately after the translated word.\n" .
            "- Example: 'મધરબોર્ડ (Motherboard)', 'રેઝિસ્ટર (Resistor)', 'વોલ્ટેજ (Voltage)'.\n" .
            "- Do NOT translate standard technical acronyms (like CPU, RAM, LED) phonetically unless necessary.\n\n" .

            "**Structure Requirements (Use Markdown):**\n" .
            "1. **Title:** Start with '# $topic'\n" .
            "2. **Aim:** Use '## Aim/Purpose'. Define the clear objective.\n" .
            "3. **Requirements:** Use '## Tools & Equipment'. List all hardware, software, materials, and safety gear required as a bulleted list.\n" .
            "4. **Theory:** Use '## Technical Theory'. Briefly explain the underlying concept or working principle.\n" .
            "5. **Procedure:** Use '## Step-by-Step Procedure'. Provide a detailed, numbered list of actionable steps. Be precise.\n" .
            "6. **Safety:** Use '## Precautions & Safety'. List critical safety warnings (e.g., high voltage, static discharge).\n" .
            "7. **Observation:** Use '## Observations/Output'. Create a blank Markdown Table or list what the student should see/measure.\n" .
            "8. **Troubleshooting:** Use '## Common Errors & Fixes'. List 3-4 real-world problems students might face and how to fix them.\n" .
            "9. **Viva Voce:** Use '## Viva Questions'. Provide 5-7 important questions with **short, accurate answers** for exam prep.\n" .
            "10. **Visuals:** Use '## Visual Reference'. Describe 2-3 diagrams or search terms for videos that explain this practical.\n\n" .

            "Format strictly as clean Markdown. No introductory filler text.";

        // --- FIX 3: Ensure $promptText matches the variable above ---
        $data = [
            "contents" => [["parts" => [["text" => $promptText]]]],
            "generationConfig" => [
                "maxOutputTokens" => (int)GEMINI_MAX_TOKENS,
                "temperature" => 0.7
            ]
        ];

        $json_data = json_encode($data);
        $url = GEMINI_API_URL . "?key=" . GEMINI_API_KEY;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            $decoded = json_decode($response, true);
            if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                $rawMarkdown = $decoded['candidates'][0]['content']['parts'][0]['text'];

                // Use Parsedown
                $Parsedown = new Parsedown();
                $Parsedown->setSafeMode(true);
                $generatedContent = $Parsedown->text($rawMarkdown);
            } else {
                $errorMsg = "Error: API refused content. Check Quota or Model.";
            }
        } else {
            $errorMsg = 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
    }
}
?>

<div id="loader" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text" id="loader-text">
        Generating content...<br>Please wait.
    </div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-flask"></i> Practical Note Generator</h1>
    <p>Generate industry-standard practicals with viva questions.</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="return handleFormSubmit();">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 2;">
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Practical Topic / Experiment Name</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: Crimping RJ45 Cable, Ohm's Law Verification" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                </div>

                <div>
                    <label style="font-weight: 600; display: block; margin-bottom: 8px;">Language</label>
                    <select name="language" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Gujarati" <?php if ($language == 'Gujarati') echo 'selected'; ?>>Gujarati</option>
                        <option value="Hindi" <?php if ($language == 'Hindi') echo 'selected'; ?>>Hindi</option>
                        <option value="English" <?php if ($language == 'English') echo 'selected'; ?>>English</option>
                    </select>
                </div>

                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">
                        <i class="fas fa-magic"></i> Generate Practical
                    </button>
                </div>
            </div>
        </form>
        <?php if ($errorMsg): ?>
            <p style="color: red; margin-top: 10px;"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
    </div>

    <?php if ($generatedContent): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3>Preview</h3>
            <div style="display: flex; gap: 10px;">
                <button onclick="exportToDocx()" class="btn-primary" style="background: #2980b9; border: none; cursor: pointer;">
                    <i class="fas fa-file-word"></i> Download DOCX
                </button>
                <button onclick="exportToPDF()" class="btn-primary" style="background: #e74c3c; border: none; cursor: pointer;">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
            </div>
        </div>

        <div class="paper-container">
            <div class="a4-page content-output <?php echo $fontClass; ?>">
                <?php echo $generatedContent; ?>
            </div>
        </div>
    <?php endif; ?>
        
</div>

<script src="https://unpkg.com/html-docx-js/dist/html-docx.js"></script>
<script src="https://unpkg.com/file-saver/dist/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // --- HELPER FUNCTIONS ---
    function showLoader(message) {
        const loader = document.getElementById('loader');
        const text = document.getElementById('loader-text');
        if (message) text.innerHTML = message; // Update text dynamically
        loader.style.display = 'flex';
    }

    function hideLoader() {
        document.getElementById('loader').style.display = 'none';
    }

    // --- DOCX EXPORT ---
    function exportToDocx() {
        // 1. Check content
        var contentBlock = document.querySelector('.content-output');
        if (!contentBlock) {
            alert("No content!");
            return;
        }

        // 2. Show Loader immediately
        showLoader("Formatting Word Document...<br>This happens in your browser.");

        // 3. Use setTimeout to allow the UI to update (show loader) before heavy processing starts
        setTimeout(function() {
            try {
                var css = `
                <style>
                    body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; }
                    h1 { font-size: 24pt; color: #1a1a1a; text-align: center; font-weight: bold; margin-bottom: 20px; }
                    h2 { font-size: 16pt; color: #0f3460; border-bottom: 1px solid #ddd; margin-top: 20px; font-weight: bold; }
                    h3 { font-size: 14pt; color: #444; margin-top: 15px; font-weight: bold; }
                    p { margin-bottom: 12px; text-align: justify; }
                    ul { margin-bottom: 15px; }
                    li { margin-bottom: 5px; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    th { background-color: #0f3460; color: white; padding: 10px; border: 1px solid #000; }
                    td { border: 1px solid #000; padding: 10px; }
                    .lang-gujarati { font-family: 'Shruti', 'Nirmala UI', 'Arial Unicode MS', serif; }
                    .lang-hindi { font-family: 'Mangal', 'Nirmala UI', 'Arial Unicode MS', serif; }
                </style>`;

                var htmlContent = `<!DOCTYPE html><html><head><meta charset="utf-8">${css}</head><body>${contentBlock.innerHTML}</body></html>`;
                var converted = htmlDocx.asBlob(htmlContent);

                var topicName = "<?php echo $topic ? htmlspecialchars($topic) : 'Practical'; ?>";
                var cleanName = topicName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                saveAs(converted, cleanName + '_practical.docx');
            } catch (err) {
                alert("Error generating DOCX: " + err.message);
            } finally {
                // 4. Hide Loader when done
                hideLoader();
            }
        }, 100); // 100ms delay to let the loader appear
    }

    function exportToPDF() {
        var element = document.querySelector('.content-output');
        if (!element) {
            alert("No content!");
            return;
        }

        // 1. Show Loader
        showLoader("Rendering PDF...<br>Calculating page breaks.");

        var topicName = "<?php echo $topic ? htmlspecialchars($topic) : 'Practical'; ?>";
        var cleanName = topicName.replace(/[^a-z0-9]/gi, '_').toLowerCase();

        var clone = element.cloneNode(true);

        // --- KEY CHANGE: WE DO NOT REDUCE PADDING HERE ---
        // We let the original CSS (20mm) handle the internal spacing.

        // Force styling for A4 PDF look
        clone.style.width = '100%';
        clone.style.height = 'auto';
        clone.style.overflow = 'visible';
        clone.style.maxHeight = 'none';
        clone.style.fontSize = '12pt';
        clone.style.lineHeight = '1.5';

        // Temporary container off-screen
        var container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.style.width = '190mm'; // Keep this slightly smaller than A4 (210mm) to prevent right-side clipping
        container.appendChild(clone);
        document.body.appendChild(container);

        var opt = {
            // --- UPDATED: ZERO MARGINS ---
            // We rely 100% on the padding inside your CSS class '.a4-page'
            margin: [10, 0, 10, 0],

            filename: cleanName + '_practical.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy']
            }
        };

        html2pdf().set(opt).from(clone).save()
            .then(function() {
                document.body.removeChild(container);
                hideLoader();
            })
            .catch(function(err) {
                console.error(err);
                alert("Error generating PDF. Please try again.");
                document.body.removeChild(container);
                hideLoader();
            });
    }

    // --- FORM SUBMIT HANDLER ---
    // We attach this to the window object so the HTML form can find it
    window.handleFormSubmit = function() {
        showLoader("Generating comprehensive practical...<br>This may take 15-30 seconds.");
        return true;
    };
</script>

<?php require_once '../templates/footer.php'; ?>