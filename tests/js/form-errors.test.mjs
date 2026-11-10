import test from 'node:test'
import assert from 'node:assert/strict'
import { registerFormErrorHandlers } from '../../resources/js/utils/formErrors.js'
import { readFile } from 'node:fs/promises'
import { parse, compileScript } from '@vue/compiler-sfc'
import { createSSRApp } from 'vue'
import { renderToString } from '@vue/server-renderer'

function setup() {
  const handlers = {}
  const alerts = []
  registerFormErrorHandlers({ on: (name, handler) => { handlers[name] = handler } }, alert => alerts.push(alert))
  return { handlers, alerts }
}

test('network failures keep the form open and explain that the save is unconfirmed', () => {
  const { handlers, alerts } = setup()
  let prevented = false
  handlers.exception({ detail: { exception: { isAxiosError: true } }, preventDefault: () => { prevented = true } })
  assert.equal(prevented, true)
  assert.equal(alerts.length, 1)
  assert.match(alerts[0].text, /Check whether your changes were saved/)
})

test('application programming errors retain their normal reporting behavior', () => {
  const { handlers, alerts } = setup()
  handlers.exception({ detail: { exception: new TypeError('test') }, preventDefault: () => assert.fail('Should not hide programming errors') })
  assert.equal(alerts.length, 0)
})

test('proxy and session failures show a safe message instead of an HTML error modal', () => {
  for (const status of [401, 403, 404, 413, 419, 422, 429, 500, 502, 503]) {
    const { handlers, alerts } = setup()
    let prevented = false
    handlers.invalid({ detail: { response: { status, data: '<html>private SQL diagnostic</html>' } }, preventDefault: () => { prevented = true } })
    assert.equal(prevented, true)
    assert.equal(alerts.length, 1)
    assert.doesNotMatch(alerts[0].text, /private SQL/)
    assert.equal(alerts[0].icon, 'error')
  }
})

test('ordinary responses do not trigger failure messages', () => {
  const { handlers, alerts } = setup()
  handlers.invalid({ detail: { response: { status: 200 } }, preventDefault: () => assert.fail('Should preserve normal responses') })
  assert.equal(alerts.length, 0)
})

test('the visible error summary escapes text, deduplicates messages, and stays hidden when valid', async () => {
  const source = await readFile(new URL('../../resources/js/Components/FormErrorSummary.vue', import.meta.url), 'utf8')
  const { descriptor } = parse(source)
  const script = compileScript(descriptor, { id: 'form-errors-test', inlineTemplate: true })
  const code = script.content.replace(/from ["']vue["']/g, `from '${import.meta.resolve('vue')}'`)
  const { default: Summary } = await import(`data:text/javascript;base64,${Buffer.from(code).toString('base64')}`)
  const html = await renderToString(createSSRApp(Summary, { errors: { name: '<script>alert(1)</script>', password: ['Try again', 'Try again'] } }))
  assert.match(html, /role="alert"/)
  assert.match(html, /&lt;script&gt;/)
  assert.doesNotMatch(html, /<script>/)
  assert.equal(html.match(/Try again/g).length, 1)
  const valid = await renderToString(createSSRApp(Summary, { errors: {} }))
  assert.doesNotMatch(valid, /role="alert"/)
})
