<?php

namespace App\Services;

class AvatarService
{
    /**
     * Generate an avatar URL with user initials using UI Avatars service
     *
     * @param string $name
     * @param int $size
     * @return string
     */
    public static function generateInitialsAvatar(string $name, int $size = 200): string
    {
        // Extract initials from name
        $initials = self::getInitials($name);
        
        // Generate random background color
        $backgroundColor = self::getRandomColor();
        
        // Use UI Avatars API to generate avatar
        // Format: https://ui-avatars.com/api/?name=John+Doe&size=200&background=random&color=fff
        return sprintf(
            'https://ui-avatars.com/api/?name=%s&size=%d&background=%s&color=fff&bold=true',
            urlencode($initials),
            $size,
            $backgroundColor
        );
    }

    /**
     * Extract initials from a full name
     *
     * @param string $name
     * @return string
     */
    private static function getInitials(string $name): string
    {
        // Remove extra spaces and split by space
        $parts = preg_split('/\s+/', trim($name));
        
        if (empty($parts)) {
            return 'U'; // Default to 'U' for User
        }

        // Get first letter of first name and last name
        if (count($parts) === 1) {
            // If only one name, use first two letters
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }

        // Get first letter of first and last name
        $firstInitial = mb_substr($parts[0], 0, 1);
        $lastInitial = mb_substr($parts[count($parts) - 1], 0, 1);
        
        return mb_strtoupper($firstInitial . $lastInitial);
    }

    /**
     * Generate a random hex color
     *
     * @return string
     */
    private static function getRandomColor(): string
    {
        $colors = [
            '3B82F6', // blue
            '8B5CF6', // purple
            'EC4899', // pink
            'F59E0B', // amber
            '10B981', // emerald
            '06B6D4', // cyan
            'EF4444', // red
            '6366F1', // indigo
        ];

        return $colors[array_rand($colors)];
    }
}
