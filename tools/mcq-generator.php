<?php
// tools/mcq-generator.php
require_once '../includes/config.php';
require_once '../includes/Parsedown.php'; // Required for Markdown conversion
require_once '../templates/header.php';

// --- 1. Prevent Timeout ---
if (function_exists('set_time_limit')) { set_time_limit(300); }

// --- Logic ---
$generatedContent = "";
$errorMsg = "";
$topic = "";
$language = "Gujarati"; 
$fontClass = "lang-gujarati";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic']);
    $language = $_POST['language'];

    switch ($language) {
        case 'Hindi': $fontClass = 'lang-hindi'; break;
        case 'English': $fontClass = 'lang-english'; break;
        default: $fontClass = 'lang-gujarati'; break;
    }

    if (!empty($topic) && defined('GEMINI_API_KEY')) {

        // 2. MAXIMIZED MARKDOWN PROMPT
        $promptText = "Act as a Subject Matter Expert. Generate an **Exhaustive MCQ Question Bank** for \"$topic\" in $language.\n\n" .
            
            "**GOAL:** Generate the MAXIMUM number of questions possible (aim for 100+). Be concise. No filler words.\n" .
            "Cover every theoretical concept, numerical value, unit, and standard data point.\n\n" .
            
            "**CRITICAL TERMINOLOGY RULE:**\n" .
            "If language is Gujarati/Hindi, you MUST write the **English Technical Term** in brackets. (e.g. 'રેઝિસ્ટન્સ (Resistance)').\n\n" .

            "**STRICT FORMATTING RULE (MARKDOWN):**\n" .
            "Do NOT use HTML. Use this exact Markdown structure:\n\n" .
            
            "# Question Bank: $topic\n\n" .
            
            "**1.** [Question Text]\n" .
            "* (A) [Option 1]\n" .
            "* (B) [Option 2]\n" .
            "* (C) [Option 3]\n" .
            "* (D) [Option 4]\n\n" .
            "**2.** [Question Text]...\n" .
            "(Repeat for as many questions as possible)\n\n" .
            
            "--- \n\n" .
            "## Answer Key\n" .
            "| Q.No | Answer | Explanation |\n" .
            "|---|---|---|\n" .
            "| 1 | (A) | [Brief Reason] |\n" .
            "| 2 | (C) | ... |\n";

        // 3. API Call
        $data = [
            "contents" => [["parts" => [["text" => $promptText]]]],
            "generationConfig" => [
                "maxOutputTokens" => 65536, // Max for Gemini Flash
                "temperature" => 0.5
            ]
        ];

        $json_data = json_encode($data);
        $ch = curl_init(GEMINI_API_URL . "?key=" . GEMINI_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            $decoded = json_decode($response, true);
            if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                $rawMarkdown = $decoded['candidates'][0]['content']['parts'][0]['text'];
                
                // 4. Convert Markdown to HTML using Parsedown
                $Parsedown = new Parsedown();
                $generatedContent = $Parsedown->text($rawMarkdown);
            } else {
                $errorMsg = "Error: API refused content. Try a smaller topic.";
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
    <div class="loading-text" id="loader-text">Generating Massive Question Bank...<br>Using efficient mode (Markdown).</div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-database"></i> MCQ Bank Generator</h1>
    <p>Optimized for maximum question generation (100+ capacity).</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="return handleFormSubmit();">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 2;">
                    <label style="font-weight: 600;">Topic</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: Indian History, Basic Electronics" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-weight: 600;">Language</label>
                    <select name="language" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Gujarati" <?php if ($language == 'Gujarati') echo 'selected'; ?>>Gujarati</option>
                        <option value="Hindi" <?php if ($language == 'Hindi') echo 'selected'; ?>>Hindi</option>
                        <option value="English" <?php if ($language == 'English') echo 'selected'; ?>>English</option>
                    </select>
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn-primary" style="width: 100%; border: none; cursor: pointer;">
                        <i class="fas fa-bolt"></i> Generate Max MCQs
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
            <div class="a4-page content-output mcq-output <?php echo $fontClass; ?>">
                <?php echo $generatedContent; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="../assets/js/html-docx.js"></script>
<script src="../assets/js/FileSaver.min.js"></script>
<script src="../assets/js/html2pdf.bundle.min.js"></script>

<script>
    const currentTopic = <?php echo json_encode($topic ? $topic : 'Question_Bank'); ?>;

    function showLoader(msg) { document.getElementById('loader-text').innerHTML = msg; document.getElementById('loader').style.display = 'flex'; }
    function hideLoader() { document.getElementById('loader').style.display = 'none'; }
    function handleFormSubmit() { showLoader("Generating Massive Question Bank...<br>This allows for 100+ questions."); return true; }

    function exportToPDF() {
        var element = document.querySelector('.content-output');
        if (!element) { alert("No content!"); return; }

        showLoader("Rendering PDF...");

        var cleanName = currentTopic.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        var clone = element.cloneNode(true);
        
        clone.style.width = '100%';
        clone.style.fontSize = '11pt';
        
        var container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.width = '190mm';
        container.appendChild(clone);
        document.body.appendChild(container);

        var opt = {
            margin: [10, 10, 10, 10],
            filename: cleanName + '_bank.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true, scrollY: 0 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opt).from(clone).save().then(() => {
            document.body.removeChild(container);
            hideLoader();
        });
    }

    function exportToDocx() {
        var contentBlock = document.querySelector('.content-output');
        if (!contentBlock) { alert("No content!"); return; }
        showLoader("Formatting Word Doc...");

        setTimeout(function() {
            var css = `
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+Gujarati:wght@400;700&display=swap');
                body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; }
                h1 { text-align: center; color: #000; font-size: 18pt; }
                h2 { border-bottom: 2px solid #000; margin-top: 20px; font-size: 14pt; }
                
                /* List Styling for Markdown */
                ul { list-style-type: none; padding-left: 0; }
                li { margin-left: 20px; margin-bottom: 2px; }
                strong { color: #000; }
                
                /* Answer Table */
                table { border-collapse: collapse; width: 100%; margin-top: 15px; }
                td, th { border: 1px solid black; padding: 5px; text-align: left; }
                th { background-color: #f0f0f0; }

                .lang-gujarati { font-family: 'Noto Serif Gujarati', 'Shruti', serif; }
            </style>`;

            var htmlContent = `<!DOCTYPE html><html><head><meta charset="utf-8">${css}</head><body>${contentBlock.innerHTML}</body></html>`;
            var converted = htmlDocx.asBlob(htmlContent);
            var cleanName = currentTopic.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            
            saveAs(converted, cleanName + '_bank.docx');
            hideLoader();
        }, 100);
    }
</script>

<?php require_once '../templates/footer.php'; ?>