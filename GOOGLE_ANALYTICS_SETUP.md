# Google Analytics & Tag Manager Implementation Guide

## Google Analytics 4 (GA4) Setup

To implement Google Analytics for the Mann Scanning Centre website:

1. **Create a Google Analytics 4 Property**
   - Go to https://analytics.google.com/
   - Create a new property for "Mann Scanning Centre"
   - Get your Measurement ID (format: G-XXXXXXXXXX)

2. **Add GA4 Tracking Code**
   Add the following code to the `<head>` section of all HTML pages:

```html
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

## Google Tag Manager (GTM) Setup

For more advanced tracking:

1. **Create GTM Container**
   - Go to https://tagmanager.google.com/
   - Create container for "mannscanningcentre.com"
   - Get Container ID (format: GTM-XXXXXXX)

2. **Add GTM Code**
   Add to `<head>`:
```html
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>
<!-- End Google Tag Manager -->
```

Add to `<body>` (immediately after opening tag):
```html
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
```

## Events to Track

Configure these events in GTM or GA4:
- Fellowship application form submissions
- Contact form submissions
- Phone number clicks
- Email clicks
- DICOM viewer link clicks
- LMP calculator usage
- Service page visits
- Download brochure clicks (if implemented)

## Google Search Console

1. Verify website ownership at https://search.google.com/search-console/
2. Submit sitemap: https://mannscanningcentre.com/sitemap.xml
3. Monitor search performance and indexing

## Privacy Policy Update

Ensure privacy policy mentions:
- Google Analytics data collection
- Cookie usage
- User data handling
- Contact information for data queries

Replace 'G-XXXXXXXXXX' and 'GTM-XXXXXXX' with actual IDs when implementing.
