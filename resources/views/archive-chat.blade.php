<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ask the archive</title>
    <style>
        :root { color-scheme: light; font: 17px/1.55 system-ui, sans-serif; color: #172923; background: #f4f6f2; }
        body { margin: 0; }
        main { width: min(680px, calc(100% - 40px)); margin: 9vh auto 3rem; }
        h1 { font: 600 clamp(2.4rem, 8vw, 4rem)/1.08 Georgia, serif; margin: 0 0 0.5rem; }
        p { margin: 0.4rem 0 1rem; }
        form, #result { background: #fff; border: 1px solid #d8e0d8; border-radius: 14px; padding: 1.3rem; margin-top: 1.5rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
        textarea { box-sizing: border-box; width: 100%; padding: 0.75rem; border: 1px solid #8e9d93; border-radius: 8px; resize: vertical; font: inherit; }
        button { border: 0; border-radius: 8px; padding: 0.65rem 1.1rem; background: #18523b; color: #fff; font: inherit; cursor: pointer; margin-top: 0.55rem; }
        button:disabled { opacity: 0.65; cursor: wait; }
        :focus-visible { outline: 3px solid #216554; outline-offset: 3px; }
        a { color: #18523b; }
        ol { padding-left: 1.5rem; }
        li p { margin: 0.3rem 0 0.9rem; }
        #answer { white-space: pre-wrap; }
    </style>
</head>
<body>
    <main>
        <h1>Ask the archive</h1>
        <p>Search indexed ordinances and resolutions in English or Filipino.</p>
        <form id="chat" data-endpoint="{{ route('archive-chat.ask', [], false) }}">
            <label for="question">Your question</label>
            <textarea id="question" name="question" rows="3" maxlength="500" required placeholder="May ordinansa ba tungkol sa solid waste?"></textarea>
            <button type="submit">Ask</button>
        </form>
        <section id="result" aria-live="polite" hidden>
            <p id="answer"></p>
            <ol id="sources" aria-label="Sources"></ol>
        </section>
    </main>
    <script>
        const form = document.querySelector('#chat');
        const button = form.querySelector('button');
        const result = document.querySelector('#result');
        const answer = document.querySelector('#answer');
        const sources = document.querySelector('#sources');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const question = form.elements.question.value.trim();
            if (!question) return;

            button.disabled = true;
            result.hidden = false;
            answer.textContent = 'Searching the archive…';
            sources.replaceChildren();

            try {
                const response = await fetch(form.dataset.endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                    body: JSON.stringify({question}),
                });
                const data = await response.json();
                answer.textContent = response.ok ? data.answer : (data.message || 'The archive could not complete the search.');

                for (const source of data.sources ?? []) {
                    const item = document.createElement('li');
                    const link = document.createElement('a');
                    const excerpt = document.createElement('p');
                    link.href = source.url;
                    link.target = '_blank';
                    link.rel = 'noopener';
                    link.textContent = `${source.title}, PDF page ${source.page}`;
                    excerpt.textContent = source.text;
                    item.append(link, excerpt);
                    sources.append(item);
                }
            } catch {
                answer.textContent = 'The local chat server could not respond.';
            } finally {
                button.disabled = false;
            }
        });
    </script>
</body>
</html>
