<?php

namespace App\Support;

class VideoEmbed
{
    public static function url(?string $videoUrl): ?string
    {
        return self::metadata($videoUrl)['embed_url'] ?? null;
    }

    /**
     * @return array{
     *   provider: string,
     *   video_id: string,
     *   embed_url: string,
     *   source_url: string,
     *   start_seconds: int
     * }|null
     */
    public static function metadata(?string $videoUrl): ?array
    {
        if (! is_string($videoUrl) || trim($videoUrl) === '') {
            return null;
        }

        $videoUrl = trim($videoUrl);
        $parts = parse_url($videoUrl);

        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = trim((string) ($parts['path'] ?? ''), '/');
        parse_str((string) ($parts['query'] ?? ''), $query);

        return self::youtubeMetadata($host, $path, $query, $videoUrl)
            ?? self::vimeoMetadata($host, $path, $videoUrl);
    }

    private static function youtubeMetadata(string $host, string $path, array $query, string $sourceUrl): ?array
    {
        $normalizedHost = preg_replace('/^www\./', '', $host) ?? $host;
        $videoId = null;

        if (in_array($normalizedHost, ['youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com'], true)) {
            if (($query['v'] ?? null) && self::isYoutubeId($query['v'])) {
                $videoId = $query['v'];
            } elseif ($path !== '') {
                $segments = explode('/', $path);
                $candidate = match ($segments[0] ?? null) {
                    'embed', 'shorts', 'live', 'v' => $segments[1] ?? null,
                    default => null,
                };

                if (self::isYoutubeId($candidate)) {
                    $videoId = $candidate;
                }
            }
        } elseif ($normalizedHost === 'youtu.be') {
            $candidate = explode('/', $path)[0] ?? null;

            if (self::isYoutubeId($candidate)) {
                $videoId = $candidate;
            }
        }

        if (! self::isYoutubeId($videoId)) {
            return null;
        }

        $startSeconds = self::youtubeStartTime($query);
        $parameters = [
            'enablejsapi' => 1,
            'rel' => 0,
        ];

        if ($startSeconds !== null) {
            $parameters['start'] = $startSeconds;
        }

        return [
            'provider' => 'youtube',
            'video_id' => $videoId,
            'embed_url' => 'https://www.youtube.com/embed/' . $videoId . '?' . http_build_query($parameters),
            'source_url' => $sourceUrl,
            'start_seconds' => $startSeconds ?? 0,
        ];
    }

    private static function vimeoMetadata(string $host, string $path, string $sourceUrl): ?array
    {
        $normalizedHost = preg_replace('/^www\./', '', $host) ?? $host;

        if (! in_array($normalizedHost, ['vimeo.com', 'player.vimeo.com'], true)) {
            return null;
        }

        if (preg_match('/(?:video\/)?(\d+)/', $path, $matches) !== 1) {
            return null;
        }

        $videoId = $matches[1];

        return [
            'provider' => 'vimeo',
            'video_id' => $videoId,
            'embed_url' => 'https://player.vimeo.com/video/' . $videoId . '?' . http_build_query(['dnt' => 1]),
            'source_url' => $sourceUrl,
            'start_seconds' => 0,
        ];
    }

    private static function youtubeStartTime(array $query): ?int
    {
        foreach (['start', 't'] as $key) {
            $value = $query[$key] ?? null;

            if (! is_string($value) && ! is_numeric($value)) {
                continue;
            }

            $value = (string) $value;

            if (ctype_digit($value)) {
                return (int) $value;
            }

            if (preg_match('/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s?)?$/i', $value, $matches) === 1) {
                $hours = (int) ($matches[1] ?? 0);
                $minutes = (int) ($matches[2] ?? 0);
                $seconds = (int) ($matches[3] ?? 0);
                $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

                if ($totalSeconds > 0) {
                    return $totalSeconds;
                }
            }
        }

        return null;
    }

    private static function isYoutubeId(?string $value): bool
    {
        return is_string($value) && preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1;
    }
}
