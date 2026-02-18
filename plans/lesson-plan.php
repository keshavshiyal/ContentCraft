<?php
// plans/lesson-plan.php
require_once '../includes/config.php';
require_once '../templates/header.php';

// --- 1. Prevent Timeout ---
if (function_exists('set_time_limit')) { set_time_limit(300); }

// --- Logic ---
$generatedContent = "";
$errorMsg = "";
$topic = "";
$next_topic = "";
$language = "Gujarati"; 
$fontClass = "lang-gujarati";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic']);
    $next_topic = trim($_POST['next_topic']);
    $language = $_POST['language'];

    // Determine Font Class
    switch ($language) {
        case 'Hindi': $fontClass = 'lang-hindi'; break;
        case 'English': $fontClass = 'lang-english'; break;
        default: $fontClass = 'lang-gujarati'; break;
    }

    if (!empty($topic) && defined('GEMINI_API_KEY')) {

        // 2. Define Headers
        $h = [
            'Gujarati' => [
                'Inst' => 'ઔધોગિક તાલીમ સંસ્થા ........................',
                'LP_Title' => 'લેશન પ્લાન',
                'Year' => 'સિલેબસનુ વર્ષ', 'Sem' => 'સેમેસ્ટર નંબર',
                'Trade' => 'ટ્રેડ', 'Sub' => 'વિષય',
                'Week' => 'વિક નંબર', 'Time' => 'ફાળવેલ સમય',
                'L_No' => 'લેશન નંબર', 'Instr' => 'સુ. ઈ. નું નામ',
                'L_Name' => 'લેશનનું નામ',
                'S1' => '૧. પૂર્વ તૈયારી :',
                'S1_1' => '૧.૧ લેશનના હેતુઓ/ લેશનના અંતે તાલીમાર્થી :', 'S1_2' => '૧.૨ સાધનો :', 'S1_3' => '૧.૩ સંદર્ભ સાહિત્ય :',
                'S2' => '૨. પ્રસ્તાવના/ પુર્વાનુંસંધાન',
                'S2_1' => '૨.૧ સમીક્ષા/ રીવ્યુ :', 'S2_2' => '૨.૨ અભીપ્રેરણા',
                'S3' => '૩. વિષય રજૂઆત :',
                'TH' => ['અ.નં.', 'હેતુઓ', 'પ્રશ્નો', 'ચાવીરૂપ માહિતી', 'સ્પોટ હિન્ટ'],
                'S4' => '૪. સારાંશ/ પુનરાવર્તન :',
                'S5' => '૫. અમલીકરણ અને સ્વાધ્યાય :',
                'S6' => '૬. હવે પછીનો પાઠ :'
            ],
            'English' => [
                'Inst' => 'Industrial Training Institute ........................',
                'LP_Title' => 'LESSON PLAN',
                'Year' => 'Syllabus Year', 'Sem' => 'Semester No',
                'Trade' => 'Trade', 'Sub' => 'Subject',
                'Week' => 'Week No', 'Time' => 'Allocated Time',
                'L_No' => 'Lesson No', 'Instr' => 'Instructor Name',
                'L_Name' => 'Lesson Name',
                'S1' => '1. PREPARATION :',
                'S1_1' => '1.1 Objectives :', 'S1_2' => '1.2 Tools & Equip :', 'S1_3' => '1.3 References :',
                'S2' => '2. INTRODUCTION',
                'S2_1' => '2.1 Review :', 'S2_2' => '2.2 Motivation',
                'S3' => '3. PRESENTATION :',
                'TH' => ['Sr.No', 'Objectives', 'Questions', 'Key Information', 'Spot Hint'],
                'S4' => '4. SUMMARY :',
                'S5' => '5. APPLICATION & ASSIGNMENT :',
                'S6' => '6. NEXT LESSON :'
            ],
            'Hindi' => [
                'Inst' => 'औद्योगिक प्रशिक्षण संस्थान ........................',
                'LP_Title' => 'पाठ योजना',
                'Year' => 'पाठ्यक्रम वर्ष', 'Sem' => 'सत्र संख्या',
                'Trade' => 'ट्रेड', 'Sub' => 'विषय',
                'Week' => 'सप्ताह संख्या', 'Time' => 'आवंटित समय',
                'L_No' => 'पाठ संख्या', 'Instr' => 'अनुदेशक का नाम',
                'L_Name' => 'पाठ का नाम',
                'S1' => '1. पूर्व तैयारी :',
                'S1_1' => '1.1 उद्देश्य :', 'S1_2' => '1.2 उपकरण :', 'S1_3' => '1.3 संदर्भ :',
                'S2' => '2. प्रस्तावना',
                'S2_1' => '2.1 समीक्षा :', 'S2_2' => '2.2 अभिप्रेरणा',
                'S3' => '3. विषय प्रस्तुति :',
                'TH' => ['क्र.सं.', 'उद्देश्य', 'प्रश्न', 'मुख्य जानकारी', 'स्पॉट हिंट'],
                'S4' => '4. सारांश :',
                'S5' => '5. अनुप्रयोग और असाइनमेंट :',
                'S6' => '6. अगला पाठ :'
            ]
        ];

        $sel = $h[$language]; 

        // 3. Prompt Engineering
        $promptText = "Act as a Senior ITI Instructor. Create a Lesson Plan for '$topic' in $language.\n" .
            "The next lesson will be: '$next_topic'.\n\n" .
            
            "**CRITICAL RULE:** If language is Gujarati/Hindi, write English technical terms in brackets. E.g. 'રેઝિસ્ટન્સ (Resistance)'.\n\n" .

            "**SECTION 3 (PRESENTATION) REQUIREMENT:**\n" .
            "This section must be extremely detailed. It should act as a 'Teaching Script'.\n" .
            "- Cover every single concept, definition, working principle, formula, and safety point.\n" .
            "- Generate **10 to 15 rows** for this table section.\n" .
            "- Ensure the 'Key Information' column has deep content so the educator can read it while teaching.\n\n" .
            
            "**VISUAL AIDS (Optional):**\n" .
            "- At the very end (after the table), suggest 2-3 specific diagrams, charts, or physical models relevant to this theory topic.\n\n" .

            "**OUTPUT FORMAT:** Generate ONLY valid HTML code. No Markdown.\n" .
            "Use this exact table structure. KEEP THE HEADER VALUES BLANK (Empty cells) as requested:\n\n" .

            "<div class='lp-form-container'>\n" .
            "  <table class='lp-table'>\n" .
            "    \n" .
            "    <tr><td colspan='5' class='text-center bold large'>{$sel['Inst']}</td></tr>\n" .
            "    <tr><td colspan='5' class='text-center bold medium'>{$sel['LP_Title']}</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Year']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Sem']} :</b></td> <td colspan='2'>&nbsp;</td>\n" . 
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Trade']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Sub']} :</b></td> <td colspan='2'>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Week']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Time']} :</b></td> <td colspan='2'>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['L_No']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Instr']} :</b></td> <td colspan='2'>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['L_Name']} :</b></td> <td colspan='4'>$topic</td>\n" .
            "    </tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S1']}</td></tr>\n" .
            "    <tr><td colspan='5'><b>{$sel['S1_1']}</b><br><ul class='clean-list'><li>[Objective 1]</li><li>[Objective 2]</li><li>[Objective 3]</li></ul></td></tr>\n" .
            "    <tr><td colspan='5'><b>{$sel['S1_2']}</b> [List All Tools & Equipment]</td></tr>\n" .
            "    <tr><td colspan='5'><b>{$sel['S1_3']}</b> [NIMI Book / Reference]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S2']}</td></tr>\n" .
            "    <tr><td colspan='5'><b>{$sel['S2_1']}</b> [Review Questions]</td></tr>\n" .
            "    <tr><td colspan='5'><b>{$sel['S2_2']}</b> [Motivation Statement]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S3']}</td></tr>\n" .
            "    <tr class='bg-gray text-center'>\n" .
            "      <th width='5%'>{$sel['TH'][0]}</th>\n" . 
            "      <th width='20%'>{$sel['TH'][1]}</th>\n" . 
            "      <th width='20%'>{$sel['TH'][2]}</th>\n" . 
            "      <th width='40%'>{$sel['TH'][3]}</th>\n" . 
            "      <th width='15%'>{$sel['TH'][4]}</th>\n" . 
            "    </tr>\n" .
            "    \n" .
            "    <tr>\n" .
            "      <td class='text-center'>1</td><td>[Obj]</td><td>[Q]</td><td>[Detailed Info]</td><td>[Hint]</td>\n" .
            "    </tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S4']}</td></tr>\n" .
            "    <tr><td colspan='5' style='height:60px; vertical-align:top;'>[Summary Points]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S5']}</td></tr>\n" .
            "    <tr><td colspan='5' style='height:60px; vertical-align:top;'>[Assignment/Task]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='5' class='section-head'>{$sel['S6']}</td></tr>\n" .
            "    <tr><td colspan='5'>$next_topic</td></tr>\n" .
            "  </table>\n\n" .
            
            "  \n" .
            "  <div class='visual-suggestions'>\n" .
            "    <b>💡 Suggested Visual Aids for Theory Class:</b><br>\n" .
            "    <ul><li>[Suggestion 1: e.g., Circuit Diagram]</li><li>[Suggestion 2: e.g., Cut-section Model]</li></ul>\n" .
            "  </div>\n" .
            "</div>";

        // 4. API Call
        $data = [
            "contents" => [["parts" => [["text" => $promptText]]]],
            "generationConfig" => ["maxOutputTokens" => 65536, "temperature" => 0.4]
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
                $rawText = preg_replace('/^```html/i', '', $rawText);
                $rawText = preg_replace('/^```/i', '', $rawText);
                $rawText = preg_replace('/```$/', '', $rawText);
                $generatedContent = $rawText;
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
    <div class="loading-text" id="loader-text">Designing Lesson Plan...<br>Compiling Detailed Teaching Points.</div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-file-contract"></i> Lesson Plan Generator</h1>
    <p>Official ITI Single-Table Format</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="return handleFormSubmit();">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 1;">
                    <label style="font-weight: 600;">Current Lesson Topic</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: Ohm's Law" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                 <div style="grid-column: span 1;">
                    <label style="font-weight: 600;">Next Lesson Topic</label>
                    <input type="text" name="next_topic" value="<?php echo htmlspecialchars($next_topic); ?>"
                        placeholder="Ex: Kirchhoff's Laws"
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
    const currentTopic = <?php echo json_encode($topic ? $topic : 'Lesson_Plan'); ?>;

    function showLoader(msg) { document.getElementById('loader-text').innerHTML = msg; document.getElementById('loader').style.display = 'flex'; }
    function hideLoader() { document.getElementById('loader').style.display = 'none'; }
    function handleFormSubmit() { showLoader("Constructing Form Layout...<br>Please wait."); return true; }

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
            filename: cleanName + '_plan.pdf',
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
                @import url('[https://fonts.googleapis.com/css2?family=Noto+Serif+Gujarati:wght@400;700&display=swap](https://fonts.googleapis.com/css2?family=Noto+Serif+Gujarati:wght@400;700&display=swap)');
                body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.3; }
                
                table.lp-table { border-collapse: collapse; width: 100%; border: 2px solid #000; }
                table.lp-table td, table.lp-table th { border: 1px solid #000; padding: 6px; vertical-align: top; }
                
                .text-center { text-align: center; }
                .bold { font-weight: bold; }
                .large { font-size: 16pt; }
                .medium { font-size: 14pt; }
                .section-head { font-weight: bold; background-color: #f0f0f0; padding: 6px; }
                .bg-gray { background-color: #e0e0e0; font-weight: bold; text-align: center; }
                .clean-list { margin: 0; padding-left: 20px; }
                
                /* Added CSS for Visual Suggestions Box */
                .visual-suggestions { 
                    margin-top: 20px; 
                    padding: 15px; 
                    border: 2px dashed #666; 
                    background-color: #fffae6; 
                    font-size: 10pt;
                }

                .lang-gujarati { font-family: 'Noto Serif Gujarati', 'Shruti', serif; }
                .lang-hindi { font-family: 'Noto Serif Devanagari', 'Mangal', serif; }
            </style>`;

            var htmlContent = `<!DOCTYPE html><html><head><meta charset="utf-8">${css}</head><body>${contentBlock.innerHTML}</body></html>`;
            var converted = htmlDocx.asBlob(htmlContent);
            var cleanName = currentTopic.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            saveAs(converted, cleanName + '_plan.docx');
            hideLoader();
        }, 100);
    }
</script>

<?php require_once '../templates/footer.php'; ?>