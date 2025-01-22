(() => {
  'use strict'

  const getStoredTheme = () => localStorage.getItem('theme')
  const setStoredTheme = theme => localStorage.setItem('theme', theme)

  // Custom event dispatch
  // const setStoredTheme = (theme) => {
  //   localStorage.setItem('theme', theme);
  //   const event = new CustomEvent('themeChanged');
  //   document.dispatchEvent(event)
  // }
  const getPreferredTheme = () => {
    // Check if there's a stored theme preference
    const storedTheme = getStoredTheme();

    if (storedTheme) {
      return storedTheme; // If a theme is stored, use it
    } else {
      // If no stored theme, set dark theme by default, else fallback to system preference
      const defaultTheme = 'dark'; // Default to dark theme
      setStoredTheme(defaultTheme); // Store the default theme
      return defaultTheme; // Return dark theme
    }
  };

  const setTheme = theme => {
    // Apply the theme to the document
    document.documentElement.setAttribute('data-bs-theme', theme);
  };

  // Set the theme based on the preferred theme
  setTheme(getPreferredTheme());


  const showActiveTheme = (theme) => {
    const themeSwitcher = document.querySelector('#theme-switcher')

    if (!themeSwitcher) {
      return
    }

    const box = document.querySelector('.box');

    if(theme === 'dark') {
      box.classList.remove('light')
      box.classList.add('dark')
    }
    if(theme ==='light') {
      box.classList.remove('dark')
      box.classList.add('light')
    }

  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = getStoredTheme()
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
      setTheme(getPreferredTheme())
    }
  })

  window.addEventListener('DOMContentLoaded', () => {
    showActiveTheme(getPreferredTheme())

    const themeSwitcher = document.querySelector('#theme-switcher')

    if (themeSwitcher) {
      if (getStoredTheme() == 'dark') {
        themeSwitcher.checked = true;
      } else if (getStoredTheme() == 'light') {
        themeSwitcher.checked = false;
      }

      themeSwitcher.addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light'
        setStoredTheme(theme)
        setTheme(theme)
        showActiveTheme(theme)
      })
    }

  })



  // Theme switch based on parameters from query string
  const urlParams = new URLSearchParams(window.location.search);
  const themeParam = urlParams.get('theme');
  if ( (themeParam === 'light') || (themeParam === 'dark')) {
    setStoredTheme(themeParam)
    setTheme(themeParam)
    showActiveTheme(themeParam)
  }

})()
