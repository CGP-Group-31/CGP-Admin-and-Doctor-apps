<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trustcare Doctor Portal</title>


    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #2E7D7A;
            --main-background: #D6EFE6;
            --section-separator: #BEE8DA;
            --section-background: #BEE8DA;
            --background: #F6F7F3;
            --container-background: #F6F7F3;
            --primary-text: #243333;
            --description-text: #6F7F7D;
            --text-shade: #7C8B89;
            --alert-non-critical: #E6B566;
            --button: #C62828;
            --white: #ffffff;
            --border: rgba(36, 51, 51, 0.08);
            --shadow: 0 16px 40px rgba(46, 125, 122, 0.10);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: var(--primary-text);
        }

        a {
            text-decoration: none;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            background: var(--background);
        }

        .main-content {
            flex: 1;
            padding: 28px;
            background: linear-gradient(180deg, #f8faf8 0%, #f3f8f6 100%);
        }

        .topbar {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px 24px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .topbar h1 {
            font-family: 'Roboto', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-text);
        }

        .topbar p {
            color: var(--description-text);
            margin-top: 6px;
            font-size: 15px;
        }

        .doctor-mini {
            text-align: right;
        }

        .doctor-mini strong {
            display: block;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
        }

        .doctor-mini span {
            color: var(--text-shade);
            font-size: 14px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #E5ECE9;
            transition: 0.2s;
        }

        .info-card h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--primary-text);
        }

        .info-card p {
            color: var(--description-text);
            font-size: 15px;
            line-height: 1.7;
        }

        .info-card:hover {
            transform: translateY(-4px);
        }

        .big-number {
            font-size: 32px;
            font-weight: 700;
            color: #2E7D7A;
            margin: 10px 0;
        }

        .wide-card {
            margin-top: 20px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .wide-card h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .wide-card p {
            color: var(--description-text);
            line-height: 1.8;
            font-size: 15px;
        }

        @media (max-width: 980px) {
            .card-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 18px;
            }
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #E5ECE9;
        }

        .dashboard-card.large {
            grid-column: span 2;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #EEF2F1;
        }

        .alert-item {
            background: #FFF4F4;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 8px;
            color: #C62828;
            font-size: 14px;
        }
    </style>
</head>
<body>