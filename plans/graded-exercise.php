<?php
// plans/graded-exercise.php
require_once '../includes/config.php';
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

    // Determine Font Class
    switch ($language) {
        case 'Hindi': $fontClass = 'lang-hindi'; break;
        case 'English': $fontClass = 'lang-english'; break;
        default: $fontClass = 'lang-gujarati'; break;
    }

    if (!empty($topic) && defined('GEMINI_API_KEY')) {

        // 2. The "Graded Exercise" Prompt
        $promptText = "Act as a Senior ITI Instructor. Create content for a 'Graded Exercise' for the job: '$topic' in $language.\n\n" .
            
            "**CRITICAL RULE:** If language is Gujarati/Hindi, write English technical terms in brackets. E.g. 'ફાઈલ (File)'.\n\n" .

            "**REQUIREMENTS:**\n" .
            "1. **AIM:** A clear, concise aim.\n" .
            "2. **PROCEDURE:** A detailed, step-by-step operational procedure (HTML <ol> list) to complete the job.\n" .
            "3. **VISUAL AIDS:** A brief suggestion of what diagrams/figures the student should draw (e.g., 'Draw Top View and Front View').\n" .
            "4. **MATERIALS:** List of raw materials (HTML <ul>).\n" .
            "5. **TOOLS:** List of tools & equipment (HTML <ul>).\n\n" .

            "**OUTPUT FORMAT:** Provide the output in a pure JSON object structure. No Markdown. No ```json tags.\n" .
            "{\n" .
            "  \"aim\": \"[Aim String]\",\n" .
            "  \"procedure\": \"[HTML Ordered List <ol><li>Step 1...</li></ol>]\",\n" .
            "  \"visual_aids\": \"[Text description of suggested figures]\",\n" .
            "  \"materials\": \"[HTML Unordered List <ul><li>...</li></ul>]\",\n" .
            "  \"tools\": \"[HTML Unordered List <ul><li>...</li></ul>]\"\n" .
            "}";

        // 3. API Call
        $data = [
            "contents" => [["parts" => [["text" => $promptText]]]],
            "generationConfig" => ["maxOutputTokens" => 4000, "temperature" => 0.4]
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
                $rawText = $decoded['candidates'][0]['content']['parts'][0]['text'];
                $rawText = preg_replace('/^```json/i', '', $rawText);
                $rawText = preg_replace('/^```/i', '', $rawText);
                $rawText = preg_replace('/```$/', '', $rawText);
                
                $aiData = json_decode($rawText, true);

                if ($aiData) {
                    // --- 4. CONSTRUCT HTML TEMPLATE (Single Table Architecture) ---
                    // Assessment Parameters (Fixed in English)
                    $assessParams = [
                        ['1', 'Safety consciousness.', '15'],
                        ['2', 'Workplace hygiene & Economical use of Material.', '10'],
                        ['3', 'Attendance/ Punctuality.', '10'],
                        ['4', 'Ability to follow Manuals/ Written instructions.', '5'],
                        ['5', 'Application of Knowledge', '10'],
                        ['6', 'Skills to handle tools & equipment.', '10'],
                        ['7', 'Speed in doing work.', '10'],
                        ['8', 'Quality in workmanship.', '15'],
                        ['9', 'VIVA', '15']
                    ];

                    // --- GENERATE TABLE ROWS ---
                    $footerRows = "";
                    $totalRows = count($assessParams); // 9 rows
                    
                    // ROW 1: Contains the rowspan content (Materials, Tools) + First Assessment Row
                    $footerRows .= "<tr>
                        <td rowspan='" . ($totalRows + 1) . "' style='vertical-align:top; font-size:10pt;'>" . $aiData['materials'] . "</td>
                        
                        <td rowspan='" . ($totalRows + 1) . "' style='vertical-align:top; font-size:10pt;'>" . $aiData['tools'] . "</td>
                        
                        <td class='text-center'>{$assessParams[0][0]}</td>
                        <td>{$assessParams[0][1]}</td>
                        <td class='text-center'>{$assessParams[0][2]}</td>
                    </tr>";

                    // ROWS 2 to 9: Assessment Rows only (Cols 3, 4, 5)
                    for ($i = 1; $i < $totalRows; $i++) {
                        $footerRows .= "<tr>
                            <td class='text-center'>{$assessParams[$i][0]}</td>
                            <td>{$assessParams[$i][1]}</td>
                            <td class='text-center'>{$assessParams[$i][2]}</td>
                        </tr>";
                    }

                    // ROW 10: TOTAL
                    $footerRows .= "<tr>
                        <td colspan='2' class='text-right bold'>TOTAL</td>
                        <td class='text-center bold'>100</td>
                    </tr>";

                    $generatedContent = "
                    <div class='lp-form-container'>
                      <table class='lp-table'>
                        <tr><td colspan='5' class='text-center bold large'>INDUSTRIAL TRAINING INSTITUTE, __________________</td></tr>
                        <tr><td colspan='5' class='text-center bold medium'>GRADED EXERCISE</td></tr>
                        
                        <tr>
                            <td width='20%'><b>Name of S.I.:</b></td> <td width='30%' colspan='2'>&nbsp;</td>
                            <td width='15%'><b>Trade:</b></td> <td width='35%'>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><b>Week No.:</b></td> <td colspan='2'>&nbsp;</td>
                            <td><b>Batch No.:</b></td> <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><b>Start Date:</b></td> <td colspan='2'>&nbsp;</td>
                            <td><b>End Date:</b></td> <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><b>Job/Exercise No.:</b></td> <td colspan='2'>&nbsp;</td>
                            <td><b>Time Given (in Hrs):</b></td> <td>&nbsp;</td>
                        </tr>
                        
                        <tr>
                            <td colspan='5' style='border-bottom: 2px solid black; padding: 10px; background-color:#f9f9f9;'>
                                <b>AIM:</b> " . $aiData['aim'] . "
                            </td>
                        </tr>

                        <tr>
                            <td colspan='5' style='vertical-align: top; padding: 15px; height: 400px;'>
                                <div style='margin-bottom: 20px;'>
                                    <b>PROCEDURE:</b>
                                    " . $aiData['procedure'] . "
                                </div>
                                <div style='border: 1px dashed #666; padding: 10px; background-color: #fffae6;'>
                                    <b>SUGGESTED FIGURES / DIAGRAMS:</b><br>
                                    " . $aiData['visual_aids'] . "
                                </div>
                            </td>
                        </tr>

                        <tr class='bg-gray text-center bold'>
                            <td width='25%'>Required Material</td>
                            <td width='25%'>Required Tools & Equipment</td>
                            <td width='5%'>&nbsp;</td> <td width='35%'>Assessment Parameter</td>
                            <td width='10%'>Break up Marks</td>
                        </tr>

                        $footerRows

                      </table>
                    </div>";

                } else {
                    $errorMsg = "Error parsing AI response. Please try again.";
                }
            } else {
                $errorMsg = "Error: API refused content.";
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
    <div class="loading-text" id="loader-text">Designing Graded Exercise...<br>Generating Procedure & Assessment Grid.</div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-check-double"></i> Graded Exercise Generator</h1>
    <p>Standard Assessment Format (Single Table Layout)</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="return handleFormSubmit();">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 1;">
                    <label style="font-weight: 600;">Job / Exercise Name</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: T-Joint Welding, Filing Practice" required
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
                        <i class="fas fa-magic"></i> Generate Plan
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

<script src="../assets/js/html-docx.js"></script>
<script src="../assets/js/FileSaver.min.js"></script>
<script src="../assets/js/html2pdf.bundle.min.js"></script>

<script>
    const currentTopic = <?php echo json_encode($topic ? $topic : 'Graded_Exercise'); ?>;

    function showLoader(msg) { document.getElementById('loader-text').innerHTML = msg; document.getElementById('loader').style.display = 'flex'; }
    function hideLoader() { document.getElementById('loader').style.display = 'none'; }
    function handleFormSubmit() { showLoader("Constructing Graded Exercise Layout..."); return true; }

    function exportToPDF() {
        var element = document.querySelector('.content-output');
        if (!element) { alert("No content!"); return; }
        showLoader("Rendering PDF...");

        var cleanName = currentTopic.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        var clone = element.cloneNode(true);
        clone.style.width = '100%';
        clone.style.fontSize = '10pt'; 

        var container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.width = '190mm';
        container.appendChild(clone);
        document.body.appendChild(container);

        var opt = {
            margin: [5, 5, 5, 5], 
            filename: cleanName + '_graded.pdf',
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
                body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.3; }
                
                table.lp-table { border-collapse: collapse; width: 100%; border: 2px solid #000; }
                table.lp-table td, table.lp-table th { border: 1px solid #000; padding: 6px; vertical-align: top; }
                
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .bold { font-weight: bold; }
                .large { font-size: 16pt; }
                .medium { font-size: 14pt; }
                .bg-gray { background-color: #f0f0f0; }
                
                /* List Styles */
                ul, ol { margin: 0; padding-left: 20px; }
                li { margin-bottom: 4px; }

                .lang-gujarati { font-family: 'Noto Serif Gujarati', 'Shruti', serif; }
                .lang-hindi { font-family: 'Noto Serif Devanagari', 'Mangal', serif; }
            </style>`;

            var htmlContent = `<!DOCTYPE html><html><head><meta charset="utf-8">${css}</head><body>${contentBlock.innerHTML}</body></html>`;
            var converted = htmlDocx.asBlob(htmlContent);
            var cleanName = currentTopic.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            saveAs(converted, cleanName + '_graded.docx');
            hideLoader();
        }, 100);
    }
</script>

<?php require_once '../templates/footer.php'; ?>