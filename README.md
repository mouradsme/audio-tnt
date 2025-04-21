# Audio Transcription and Translation System

A web application that allows dual-speaker audio recording, transcription, and translation with similarity comparison.

## Features

- Dual speaker audio recording
- Real-time transcription
- Multi-language support
- Translation comparison
- Similarity score calculation
- Modern, responsive UI

## Prerequisites

- PHP 8.0 or higher
- Composer
- Node.js and npm
- FFmpeg (for audio processing)
- Whisper (for transcription)
- Web browser with Web Audio API support

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create and configure the environment file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Update the `.env` file with your configuration:
```env
APP_NAME="Audio Transcription"
APP_ENV=local
APP_KEY=your-generated-key
APP_DEBUG=true
APP_URL=http://localhost

# Database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Set up the database:
```bash
php artisan migrate
```

7. Install FFmpeg:
```bash
# On Ubuntu/Debian
sudo apt-get install ffmpeg

# On macOS
brew install ffmpeg

# On Windows
# Download from https://ffmpeg.org/download.html
```

8. Install Whisper:
```bash
pip install git+https://github.com/openai/whisper.git
```

## Usage

1. Start the development server:
```bash
php artisan serve
```

2. Open your browser and navigate to:
```
http://localhost:8000
```

3. Using the application:
   - Select languages for each speaker
   - Click "Start Recording" to begin recording
   - Click "Stop Recording" to stop and process the audio
   - Select a target language for translation
   - Click "Compare Translations" to see the similarity score

## Directory Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AudioTranscriptionController.php
│   │   │   └── TranslationController.php
│   │   └── Middleware/
├── resources/
│   ├── views/
│   │   └── audio_listener_test.blade.php
│   └── js/
├── public/
├── routes/
│   └── web.php
└── storage/
    └── app/
        └── public/
```

## API Endpoints

- `POST /transcribe`
  - Accepts audio file and language
  - Returns transcription

- `POST /translate`
  - Accepts text and target language
  - Returns translation

## Browser Support

The application requires a modern browser with Web Audio API support:
- Chrome 49+
- Firefox 36+
- Safari 11+
- Edge 79+

## Troubleshooting

1. **Audio Recording Issues**
   - Ensure microphone permissions are granted
   - Check browser console for errors
   - Verify Web Audio API support

2. **Transcription Issues**
   - Verify FFmpeg installation
   - Check Whisper installation
   - Ensure sufficient system resources

3. **Translation Issues**
   - Check internet connection
   - Verify API endpoint availability
   - Monitor rate limits

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- OpenAI Whisper for transcription
- Google Translate API for translations
- Laravel framework
- Bootstrap for UI components
