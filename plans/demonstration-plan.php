<?php
// plans/demonstration-plan.php
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

        // 2. Define Headers (Demonstration Plan Format)
        $h = [
            'Gujarati' => [
                'Inst' => 'ઔધોગિક તાલીમ સંસ્થા ........................',
                'DP_Title' => 'ડેમોન્સ્ટ્રેશન પ્લાન',
                'Year' => 'સિલેબસ વર્ષ', 'Sem' => 'સેમેસ્ટર નંબર',
                'Trade' => 'ટ્રેડ', 'Sub' => 'વિષય',
                'Ex_No' => 'એકસરસાઇઝ નંબર', 'Week' => 'વિક નંબર',
                'Time' => 'ફાળવેલ સમય', 'Instr' => 'સુ. ઈ. નું નામ',
                'Title' => 'ટાઈટલ',
                'S1' => '૧. પૂર્વ તૈયારી',
                'S1_1' => '૧.૧ હેતુઓ : નિદર્શન ના અંતે તાલીમાર્થીઓ :', 
                'S1_2' => '૧.૨ તાલીમ માટે સાધનો/ સાહિત્ય :', 
                'S1_3' => '૧.૩ નિદર્શન માટે કાચો માલ અને ઉપકરણો :',
                'S1_4' => '૧.૪ સંદર્ભ સાહિત્ય',
                'S2' => '૨. પ્રસ્તાવના/ પુર્વાનુંસંધાન',
                'S2_1' => '૨.૧ સમીક્ષા (રીવ્યુ) અને પુર્વાનુંસંધાન (લિંક) :', 
                'S2_2' => '૨.૨ અભીપ્રેરીત કરવાના મુદ્દા અને પુર્વાનુંસંધાન :',
                'S3' => '૩. રજૂઆત/ નિદર્શનના ક્રમિક પદ',
                'TH' => ['અનુ. નં.', 'નિદર્શન દરમ્યાન અનુસરવાના ક્રમિક પદો', 'ચાવીરૂપ પ્રવૃતિ/ મુદ્દા'],
                'S4' => '૪. અમલીકરણ (માર્ગદર્શન હેઠળ તાલીમાર્થીઓની પ્રવૃતિ) :',
                'S5' => '૫. કસોટી/ તાલીમાર્થીઓની સ્વતંત્ર કામગીરી',
                'S6' => '૬. સ્વાધ્યાય (ગૃહકાર્ય)',
                'S7' => '૭. હવે પછી અનુસરવાનું પ્રાયોગિક કામ'
            ],
            'English' => [
                'Inst' => 'Industrial Training Institute ........................',
                'DP_Title' => 'DEMONSTRATION PLAN',
                'Year' => 'Syllabus Year', 'Sem' => 'Semester No',
                'Trade' => 'Trade', 'Sub' => 'Subject',
                'Ex_No' => 'Exercise No', 'Week' => 'Week No',
                'Time' => 'Allocated Time', 'Instr' => 'Instructor Name',
                'Title' => 'Title',
                'S1' => '1. PREPARATION',
                'S1_1' => '1.1 Objectives :', 
                'S1_2' => '1.2 Tools & Equipment :', 
                'S1_3' => '1.3 Raw Materials :',
                'S1_4' => '1.4 References',
                'S2' => '2. INTRODUCTION',
                'S2_1' => '2.1 Review & Link :', 
                'S2_2' => '2.2 Motivation :',
                'S3' => '3. PRESENTATION / DEMONSTRATION STEPS',
                'TH' => ['Sr.No', 'Steps to follow during demonstration', 'Key Points / Safety Hints'],
                'S4' => '4. APPLICATION (Guided Practice) :',
                'S5' => '5. TEST (Independent Practice)',
                'S6' => '6. ASSIGNMENT (Homework)',
                'S7' => '7. NEXT PRACTICAL WORK'
            ],
            'Hindi' => [
                'Inst' => 'औद्योगिक प्रशिक्षण संस्थान ........................',
                'DP_Title' => 'प्रदर्शन योजना',
                'Year' => 'पाठ्यक्रम वर्ष', 'Sem' => 'सत्र संख्या',
                'Trade' => 'ट्रेड', 'Sub' => 'विषय',
                'Ex_No' => 'अभ्यास संख्या', 'Week' => 'सप्ताह संख्या',
                'Time' => 'आवंटित समय', 'Instr' => 'अनुदेशक का नाम',
                'Title' => 'शीर्षक',
                'S1' => '1. पूर्व तैयारी',
                'S1_1' => '1.1 उद्देश्य :', 
                'S1_2' => '1.2 उपकरण और साधन :', 
                'S1_3' => '1.3 कच्चा माल :',
                'S1_4' => '1.4 संदर्भ',
                'S2' => '2. प्रस्तावना',
                'S2_1' => '2.1 समीक्षा :', 
                'S2_2' => '2.2 अभिप्रेरणा :',
                'S3' => '3. प्रस्तुति / प्रदर्शन के चरण',
                'TH' => ['क्र.सं.', 'प्रदर्शन के दौरान अनुसरण किए जाने वाले चरण', 'मुख्य बिंदु / सुरक्षा संकेत'],
                'S4' => '4. अनुप्रयोग (निर्देशित अभ्यास) :',
                'S5' => '5. परीक्षण (स्वतंत्र अभ्यास)',
                'S6' => '6. असाइनमेंट (गृहकार्य)',
                'S7' => '7. अगला व्यावहारिक कार्य'
            ]
        ];

        $sel = $h[$language]; 

        // 3. The "Demonstration Architect" Prompt
        $promptText = "Act as a Senior ITI Instructor giving a Practical Demonstration. Create a Demonstration Plan for the experiment: '$topic' in $language.\n" .
            "The next practical will be: '$next_topic'.\n\n" .
            
            "**CRITICAL RULE:** If language is Gujarati/Hindi, write English technical terms in brackets.\n\n" .

            "**SECTION 3 (PRESENTATION) REQUIREMENT:**\n" .
            "- This is a PRACTICAL DEMO. Steps must be actionable (e.g., 'Connect the wire', 'Check voltage').\n" .
            "- Include safety precautions in the 'Key Points' column.\n" .
            "- Generate 8-12 detailed steps.\n\n" .

            "**VISUAL AIDS (Optional):**\n" .
            "- If relevant, at the very end of the output (after the table), create a separate div with suggestions for diagrams, charts, or models that would help explain this demo.\n\n" .

            "**OUTPUT FORMAT:** Generate ONLY valid HTML code. No Markdown.\n" .
            "Use this exact Single-Table structure with BLANK header values:\n\n" .

            "<div class='lp-form-container'>\n" .
            "  <table class='lp-table'>\n" .
            "    \n" .
            "    <tr><td colspan='4' class='text-center bold large'>{$sel['Inst']}</td></tr>\n" .
            "    <tr><td colspan='4' class='text-center bold medium'>{$sel['DP_Title']}</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr>\n" .
            "      <td width='25%'><b>{$sel['Year']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td width='25%'><b>{$sel['Sem']} :</b></td> <td>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Trade']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Sub']} :</b></td> <td>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Ex_No']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Week']} :</b></td> <td>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr>\n" .
            "      <td><b>{$sel['Time']} :</b></td> <td>&nbsp;</td>\n" .
            "      <td><b>{$sel['Instr']} :</b></td> <td>&nbsp;</td>\n" .
            "    </tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['Title']} :</b> $topic</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S1']}</td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S1_1']}</b><br><ul class='clean-list'><li>[Objective 1]</li><li>[Objective 2]</li><li>[Objective 3]</li></ul></td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S1_2']}</b> [List Tools]</td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S1_3']}</b> [List Raw Materials]</td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S1_4']}</b> [References]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S2']}</td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S2_1']}</b> [Review/Link]</td></tr>\n" .
            "    <tr><td colspan='4'><b>{$sel['S2_2']}</b> [Motivation]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S3']}</td></tr>\n" .
            "    <tr class='bg-gray text-center'>\n" .
            "      <th width='10%'>{$sel['TH'][0]}</th>\n" . // Sr No
            "      <th width='50%'>{$sel['TH'][1]}</th>\n" . // Steps
            "      <th width='40%' colspan='2'>{$sel['TH'][2]}</th>\n" . // Key Points
            "    </tr>\n" .
            "    \n" .
            "    <tr>\n" .
            "      <td class='text-center'>1</td><td>[Step Description]</td><td colspan='2'>[Key Point/Safety]</td>\n" .
            "    </tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S4']}</td></tr>\n" .
            "    <tr><td colspan='4' style='height:50px;'>[Instructor Guidance Points]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S5']}</td></tr>\n" .
            "    <tr><td colspan='4' style='height:50px;'>[Evaluation Criteria]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S6']}</td></tr>\n" .
            "    <tr><td colspan='4' style='height:40px;'>[Homework Task]</td></tr>\n" .
            "    \n" .
            "    \n" .
            "    <tr><td colspan='4' class='section-head'>{$sel['S7']}</td></tr>\n" .
            "    <tr><td colspan='4'>$next_topic</td></tr>\n" .
            "  </table>\n\n" .
            
            "  \n" .
            "  <div class='visual-suggestions'>\n" .
            "    <b>💡 Suggested Visual Aids for Demo:</b><br>\n" .
            "    <ul><li>[Suggestion 1: e.g., Circuit Diagram of X]</li><li>[Suggestion 2: e.g., Cut-section Model of Y]</li></ul>\n" .
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
    <div class="loading-text" id="loader-text">Designing Demo Plan...<br>Organizing Practical Steps.</div>
</div>

<section class="hero" style="padding: 2rem 1rem; min-height: auto;">
    <h1><i class="fas fa-tools"></i> Demonstration Plan Generator</h1>
    <p>Official ITI Practical Demo Format</p>
</section>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow); margin-bottom: 30px;">
        <form method="POST" action="" onsubmit="return handleFormSubmit();">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="grid-column: span 1;">
                    <label style="font-weight: 600;">Practical / Experiment Title</label>
                    <input type="text" name="topic" value="<?php echo htmlspecialchars($topic); ?>"
                        placeholder="Ex: Wiring a Staircase Switch" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                 <div style="grid-column: span 1;">
                    <label style="font-weight: 600;">Next Practical Title</label>
                    <input type="text" name="next_topic" value="<?php echo htmlspecialchars($next_topic); ?>"
                        placeholder="Ex: Godown Wiring"
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
    const currentTopic = <?php echo json_encode($topic ? $topic : 'Demo_Plan'); ?>;

    function showLoader(msg) { document.getElementById('loader-text').innerHTML = msg; document.getElementById('loader').style.display = 'flex'; }
    function hideLoader() { document.getElementById('loader').style.display = 'none'; }
    function handleFormSubmit() { showLoader("Constructing Demo Layout..."); return true; }

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
            filename: cleanName + '_demo.pdf',
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
                
                /* Visual Suggestions Box Style */
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
            saveAs(converted, cleanName + '_demo.docx');
            hideLoader();
        }, 100);
    }
</script>

<?php require_once '../templates/footer.php'; ?>