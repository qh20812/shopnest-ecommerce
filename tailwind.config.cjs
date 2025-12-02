import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
        './resources/js/**/*.ts',
        './resources/js/**/*.jsx',
        './resources/js/**/*.js',
        './resources/css/**/*.css',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                /* Palette from user */
                light: '#e6f4f1',
                primary: {
                    DEFAULT: '#2563eb',
                    light: '#CFE8FF',
                },
                dark: '#464555',
                grey: '#aaa9bc',
                neutral: '#ffefca',
                muted: 'var(--muted)',

                /* semantic mappings */
                secondary: {
                    DEFAULT: '#8d8db9',
                    light: '#c49c00',
                },
                accent: {
                    DEFAULT: '#cf52d0',
                    light: '#ff599f',
                },
                info: {
                    DEFAULT: '#008bb4',
                    light: '#e6f4f1',
                },
                danger: {
                    DEFAULT: '#dc003e',
                },
                tertiary: {
                    DEFAULT: '#c49c00',
                    light: '#ffc358',
                },
                warning: {
                    DEFAULT: '#ffc358',
                    light: '#f9f871',
                },
                success: {
                    DEFAULT: '#008bb4',
                },

                /* card / muted / border helper colors (map to CSS vars) */
                card: 'var(--card)',
                'card-foreground': 'var(--card-foreground)',
                muted: 'var(--muted)',
                'muted-foreground': 'var(--muted-foreground)',
                border: 'var(--border)',
                input: 'var(--input)',
                ring: 'var(--ring)',

                /* additional usage colors */
                olive: '#847655',
                tan: '#c0a975',
                cream: '#ffefca',
                'orange-brown': '#ae5500',
                coral: '#ff8970',
                pink: '#ff599f',
            },
        },
    },
    plugins: [forms],
};
