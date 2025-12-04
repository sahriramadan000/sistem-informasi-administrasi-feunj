/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Project Brand Colors - Orange as Primary
        brand: {
          primary: '#FF4D00',    // Main orange color
          secondary: '#FFFFFF',   // White
          50: '#FFF5F0',          // Lightest orange
          100: '#FFE8DB',
          200: '#FFD1B8',
          300: '#FFBA94',
          400: '#FFA370',
          500: '#FF4D00',         // Base orange
          600: '#E64500',
          700: '#CC3D00',
          800: '#B33500',
          900: '#992D00',
          950: '#661E00',         // Darkest orange
          DEFAULT: '#FF4D00',     // Default brand color
        },
        
        // Complementary Colors for Harmonious Design
        success: {
          50: '#ECFEFF',
          100: '#CFFAFE',
          200: '#A5F3FC',
          300: '#67E8F9',
          400: '#22D3EE',
          500: '#0891B2',   // Teal - complementary to orange
          600: '#0E7490',
          700: '#155E75',
          800: '#164E63',
          900: '#083344',
          DEFAULT: '#0891B2',
        },
        
        info: {
          50: '#EEF2FF',
          100: '#E0E7FF',
          200: '#C7D2FE',
          300: '#A5B4FC',
          400: '#818CF8',
          500: '#4F46E5',   // Indigo - professional and modern
          600: '#4338CA',
          700: '#3730A3',
          800: '#312E81',
          900: '#1E1B4B',
          DEFAULT: '#4F46E5',
        },
        
        warning: {
          50: '#FFFBEB',
          100: '#FEF3C7',
          200: '#FDE68A',
          300: '#FCD34D',
          400: '#FBBF24',
          500: '#F59E0B',   // Amber - same family as orange
          600: '#D97706',
          700: '#B45309',
          800: '#92400E',
          900: '#78350F',
          DEFAULT: '#F59E0B',
        },
        
        // Existing shadcn/ui colors
        border: "hsl(var(--border))",
        input: "hsl(var(--input))",
        ring: "hsl(var(--ring))",
        background: "hsl(var(--background))",
        foreground: "hsl(var(--foreground))",
        primary: {
          DEFAULT: "hsl(var(--primary))",
          foreground: "hsl(var(--primary-foreground))",
        },
        secondary: {
          DEFAULT: "hsl(var(--secondary))",
          foreground: "hsl(var(--secondary-foreground))",
        },
        destructive: {
          DEFAULT: "hsl(var(--destructive))",
          foreground: "hsl(var(--destructive-foreground))",
        },
        muted: {
          DEFAULT: "hsl(var(--muted))",
          foreground: "hsl(var(--muted-foreground))",
        },
        accent: {
          DEFAULT: "hsl(var(--accent))",
          foreground: "hsl(var(--accent-foreground))",
        },
        popover: {
          DEFAULT: "hsl(var(--popover))",
          foreground: "hsl(var(--popover-foreground))",
        },
        card: {
          DEFAULT: "hsl(var(--card))",
          foreground: "hsl(var(--card-foreground))",
        },
      },
      ringColor: {
        brand: {
          50: '#FFF5F0',
          100: '#FFE8DB',
          200: '#FFD1B8',
          300: '#FFBA94',
          400: '#FFA370',
          500: '#FF4D00',
          600: '#E64500',
          700: '#CC3D00',
          800: '#B33500',
          900: '#992D00',
          950: '#661E00',
        },
      },
      borderRadius: {
        lg: "var(--radius)",
        md: "calc(var(--radius) - 2px)",
        sm: "calc(var(--radius) - 4px)",
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
