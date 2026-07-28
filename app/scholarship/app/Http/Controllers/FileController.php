<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * Stream an uploaded document (applicant photo, certificate, etc.).
     *
     * The route previously resolved the filename straight into a path, so a
     * request such as /storage/uploads/..%2F..%2F.env served arbitrary files
     * from the server. The filename is now restricted to a single path segment
     * by the route pattern and re-checked here, and the resolved path must stay
     * inside the uploads directory.
     */
    public function show(string $filename): BinaryFileResponse
    {
        $uploads = realpath(storage_path('uploads'));

        if ($uploads === false) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // basename() strips any directory portion that survived the route pattern.
        $path = realpath($uploads.DIRECTORY_SEPARATOR.basename($filename));

        if ($path === false || ! is_file($path) || ! str_starts_with($path, $uploads.DIRECTORY_SEPARATOR)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return response()->file($path);
    }
}
