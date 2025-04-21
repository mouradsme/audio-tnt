<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AudioTranscriptionController extends Controller
{
    public function transcribe(Request $request)
    {
        try {
            $request->validate([
                'audio' => 'required|file|mimes:webm,mp3,wav,ogg|max:10240', // 10MB max
                'language' => 'required|string|size:2',
                'speaker_id' => 'required|integer|in:1,2'
            ]);

            if (!$request->hasFile('audio')) {
                return response()->json(['error' => 'No audio file provided'], 400);
            }

            $audioFile = $request->file('audio');
            $language = $request->input('language');
            $speakerId = $request->input('speaker_id');
            
            // Create temp directory if it doesn't exist
            $tempDir = 'temp';
            if (!Storage::exists($tempDir)) {
                Storage::makeDirectory($tempDir);
            }

            // Save the file temporarily
            $inputFileName = 'input_' . $speakerId . '_' . time() . '.' . $audioFile->getClientOriginalExtension();
            $inputPath = $audioFile->storeAs($tempDir, $inputFileName);
            $outputFileName = pathinfo($inputFileName, PATHINFO_FILENAME) . '.txt';
            $outputPath = $tempDir . '/' . $outputFileName;

            \Log::info('Processing audio file', [
                'speaker_id' => $speakerId,
                'language' => $language,
                'original_name' => $audioFile->getClientOriginalName(),
                'mime_type' => $audioFile->getMimeType(),
                'size' => $audioFile->getSize(),
                'input_path' => $inputPath,
                'output_path' => $outputPath
            ]);

            // Get the full path for the input file
            $fullInputPath = Storage::path($inputPath);
            $fullOutputDir = Storage::path($tempDir);

            // Run Whisper command with language parameter
            $command = sprintf(
                'whisper "%s" --model base --output_format txt --output_dir "%s" --language %s --task transcribe',
                $fullInputPath,
                $fullOutputDir,
                escapeshellarg($language)
            );

            \Log::info('Executing command: ' . $command);
            
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            \Log::info('Whisper command output', [
                'return_var' => $returnVar,
                'output' => $output,
                'temp_dir_contents' => Storage::files($tempDir)
            ]);

            if ($returnVar !== 0) {
                \Log::error('Whisper command failed', [
                    'return_var' => $returnVar,
                    'output' => $output
                ]);
                return response()->json(['error' => 'Transcription failed: ' . implode("\n", $output)], 500);
            }

            if (!Storage::exists($outputPath)) {
                \Log::error('Output file not found', [
                    'expected_path' => $outputPath,
                    'directory_contents' => Storage::files($tempDir)
                ]);
                return response()->json(['error' => 'Transcription output not found. Check server logs for details.'], 500);
            }

            $transcription = Storage::get($outputPath);

            // Clean up temporary files
            Storage::delete([$inputPath, $outputPath]);

            return response()->json([
                'transcription' => $transcription,
                'speaker_id' => $speakerId,
                'language' => $language
            ]);

        } catch (\Exception $e) {
            \Log::error('Transcription error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 