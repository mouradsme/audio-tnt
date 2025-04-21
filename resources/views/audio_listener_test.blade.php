<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Audio Transcription and Translation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f5f5f5;
            --success-color: #28a745;
            --error-color: #dc3545;
            --warning-color: #ffc107;
        }

        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .speaker-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .speaker-section:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #357abd;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--error-color);
            border: none;
        }

        .btn-success {
            background-color: var(--success-color);
            border: none;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-recording {
            background-color: var(--error-color);
            animation: pulse 1.5s infinite;
        }

        .status-processing {
            background-color: var(--warning-color);
            animation: pulse 1.5s infinite;
        }

        .status-ready {
            background-color: var(--success-color);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .transcription-result {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .translation-result {
            background-color: #e8f4f8;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            border-left: 4px solid var(--success-color);
        }

        .select-language {
            width: 100%;
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid #ced4da;
            margin-bottom: 1rem;
        }

        .btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn.loading .loading-spinner {
            display: inline-block;
        }

        .btn-text {
            margin-left: 8px;
        }

        .error-message {
            color: var(--error-color);
            background-color: #f8d7da;
            padding: 0.5rem;
            border-radius: 5px;
            margin-top: 0.5rem;
            display: none;
        }

        .translation-comparison {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .translation-pair {
            display: flex;
            gap: 1.5rem;
            margin: 1rem 0;
        }

        .translation-box {
            flex: 1;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid var(--primary-color);
        }

        .translation-box h5 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .similarity-indicator {
            text-align: center;
            padding: 1rem;
            border-radius: 5px;
            color: white;
            margin-top: 1rem;
        }

        .similarity-value {
            font-size: 2rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .translation-box p {
            margin: 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Audio Transcription and Translation</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="speaker-section">
                    <h3>Speaker 1</h3>
                    <select class="select-language" id="speaker1Language">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="es">Spanish</option>
                        <option value="de">German</option>
                    </select>
                    <button class="btn btn-primary" id="startRecording1">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Start Recording</span>
                    </button>
                    <button class="btn btn-danger" id="stopRecording1" style="display: none;">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Stop Recording</span>
                    </button>
                    <div class="transcription-result" id="transcription1"></div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="speaker-section">
                    <h3>Speaker 2</h3>
                    <select class="select-language" id="speaker2Language">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="es">Spanish</option>
                        <option value="de">German</option>
                    </select>
                    <button class="btn btn-primary" id="startRecording2">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Start Recording</span>
                    </button>
                    <button class="btn btn-danger" id="stopRecording2" style="display: none;">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Stop Recording</span>
                    </button>
                    <div class="transcription-result" id="transcription2"></div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <select class="select-language" id="targetLanguage">
                <option value="en">English</option>
                <option value="fr">French</option>
                <option value="es">Spanish</option>
                <option value="de">German</option>
            </select>
            <button class="btn btn-success" id="compareTranslations">
                <span class="loading-spinner"></span>
                <span class="btn-text">Compare Translations</span>
            </button>
        </div>

        <div class="translation-result mt-4" id="translationResult"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up axios defaults with CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        class SpeakerRecorder {
            constructor(speakerId) {
                this.speakerId = speakerId;
                this.mediaRecorder = null;
                this.audioChunks = [];
                this.startButton = document.getElementById(`startRecording${speakerId}`);
                this.stopButton = document.getElementById(`stopRecording${speakerId}`);
                this.transcriptionDiv = document.getElementById(`transcription${speakerId}`);
                this.languageSelect = document.getElementById(`speaker${speakerId}Language`);
                
                this.setupEventListeners();
            }

            setupEventListeners() {
                this.startButton.addEventListener('click', () => this.startRecording());
                this.stopButton.addEventListener('click', () => this.stopRecording());
            }

            async startRecording() {
                try {
                    this.startButton.classList.add('loading');
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.audioChunks = [];

                    this.mediaRecorder.addEventListener('dataavailable', event => {
                        this.audioChunks.push(event.data);
                    });

                    this.mediaRecorder.addEventListener('stop', () => {
                        this.sendAudioToServer();
                    });

                    this.mediaRecorder.start();
                    this.startButton.style.display = 'none';
                    this.stopButton.style.display = 'inline-block';
                    this.startButton.classList.remove('loading');
                } catch (error) {
                    console.error('Error starting recording:', error);
                    this.showError('Failed to start recording. Please check your microphone permissions.');
                    this.startButton.classList.remove('loading');
                }
            }

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                    this.stopButton.classList.add('loading');
                    this.mediaRecorder.stop();
                    this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                }
            }

            async sendAudioToServer() {
                try {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const formData = new FormData();
                    formData.append('audio', audioBlob, 'recording.webm');
                    formData.append('language', this.languageSelect.value);
                    formData.append('speaker_id', this.speakerId);

                    const response = await axios.post('/transcribe', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    this.transcriptionDiv.innerHTML = `
                        <strong>Transcription:</strong> ${response.data.transcription}
                    `;
                } catch (error) {
                    console.error('Error sending audio:', error);
                    this.showError('Failed to transcribe audio. Please try again.');
                } finally {
                    this.stopButton.classList.remove('loading');
                    this.startButton.style.display = 'inline-block';
                    this.stopButton.style.display = 'none';
                }
            }

            showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                this.transcriptionDiv.appendChild(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        }

        class TranslationManager {
            constructor() {
                this.compareButton = document.getElementById('compareTranslations');
                this.translationResult = document.getElementById('translationResult');
                this.targetLanguageSelect = document.getElementById('targetLanguage');
                
                this.setupEventListeners();
            }

            setupEventListeners() {
                this.compareButton.addEventListener('click', () => this.compareTranslations());
            }

            calculateSimilarity(str1, str2) {
                // Convert to lowercase and remove punctuation
                const cleanStr1 = str1.toLowerCase().replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '');
                const cleanStr2 = str2.toLowerCase().replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '');
                
                // Split into words
                const words1 = cleanStr1.split(/\s+/).filter(word => word.length > 0);
                const words2 = cleanStr2.split(/\s+/).filter(word => word.length > 0);
                
                // Create sets of unique words
                const set1 = new Set(words1);
                const set2 = new Set(words2);
                
                // Calculate intersection and union
                const intersection = new Set([...set1].filter(x => set2.has(x)));
                const union = new Set([...set1, ...set2]);
                
                // Calculate similarity percentage
                const similarity = (intersection.size / union.size) * 100;
                return Math.round(similarity * 100) / 100; // Round to 2 decimal places
            }

            async compareTranslations() {
                const transcription1 = document.getElementById('transcription1').textContent;
                const transcription2 = document.getElementById('transcription2').textContent;
                const targetLanguage = this.targetLanguageSelect.value;

                if (!transcription1 || !transcription2) {
                    this.showError('Please record and transcribe both speakers first.');
                    return;
                }

                try {
                    this.compareButton.classList.add('loading');
                    
                    const response1 = await axios.post('/translate', {
                        text: transcription1,
                        target_language: targetLanguage
                    });

                    const response2 = await axios.post('/translate', {
                        text: transcription2,
                        target_language: targetLanguage
                    });

                    const translated1 = response1.data.translation;
                    const translated2 = response2.data.translation;
                    const similarity = this.calculateSimilarity(translated1, translated2);

                    // Create a color based on similarity percentage
                    const color = this.getSimilarityColor(similarity);

                    this.translationResult.innerHTML = `
                        <div class="translation-comparison">
                            <h4>Translation Results</h4>
                            <div class="translation-pair">
                                <div class="translation-box">
                                    <h5>Speaker 1</h5>
                                    <p>${translated1}</p>
                                </div>
                                <div class="translation-box">
                                    <h5>Speaker 2</h5>
                                    <p>${translated2}</p>
                                </div>
                            </div>
                            <div class="similarity-indicator" style="background-color: ${color}">
                                <h5>Similarity Score</h5>
                                <div class="similarity-value">${similarity}%</div>
                            </div>
                        </div>
                    `;
                } catch (error) {
                    console.error('Error comparing translations:', error);
                    this.showError('Failed to translate text. Please try again.');
                } finally {
                    this.compareButton.classList.remove('loading');
                }
            }

            getSimilarityColor(similarity) {
                // Color gradient from red (0%) to green (100%)
                const hue = (similarity * 120) / 100; // 0 is red, 120 is green
                return `hsl(${hue}, 70%, 50%)`;
            }

            showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                this.translationResult.appendChild(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            new SpeakerRecorder(1);
            new SpeakerRecorder(2);
            new TranslationManager();
        });
    </script>
</body>
</html> 