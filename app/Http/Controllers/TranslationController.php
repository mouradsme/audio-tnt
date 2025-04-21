<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    public function translate(Request $request)
    {
        try {
            $request->validate([
                'text' => 'required|string',
                'target_language' => 'required|string|size:2'
            ]);

            $text = $request->input('text');
            $targetLanguage = $request->input('target_language');

            \Log::info('Translating text', [
                'text' => $text,
                'target_language' => $targetLanguage
            ]);

            // Build the Google Translate API URL
            $url = sprintf(
                'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=%s&dt=t&q=%s',
                urlencode($targetLanguage),
                urlencode($text)
            );

            // Make the request to Google Translate API
            $response = file_get_contents($url);
            
            if ($response === false) {
                throw new \Exception('Failed to connect to translation service');
            }

            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid response from translation service');
            }

            if (!isset($data[0]) || !is_array($data[0])) {
                throw new \Exception('Unexpected response format from translation service');
            }

            // Extract the translated text
            $translatedText = '';
            foreach ($data[0] as $segment) {
                if (isset($segment[0])) {
                    $translatedText .= $segment[0];
                }
            }

            // Get the detected source language
            $sourceLanguage = $data[2] ?? 'auto';

            \Log::info('Translation successful', [
                'original' => $text,
                'translated' => $translatedText,
                'source_language' => $sourceLanguage,
                'target_language' => $targetLanguage
            ]);

            return response()->json([
                'translation' => $translatedText,
                'target_language' => $targetLanguage,
                'source_language' => $sourceLanguage
            ]);

        } catch (\Exception $e) {
            \Log::error('Translation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 