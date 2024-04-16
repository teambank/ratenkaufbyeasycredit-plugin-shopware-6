
// sw6.4 only
export const getCsrfToken = async () => {
  let token;
  if (typeof window.csrf === 'undefined') {
    return;
  }

  let csrf = window.csrf;
  if (
	csrf.enabled === '1' &&
	csrf.mode === 'ajax'
  ) {
	token = await fetch(window.router['frontend.csrf.generateToken'], { method:'POST', headers: new Headers({'content-type': 'application/json'}) })
	  .then(resp => resp.json())
	  .then(resp => resp.token);
  }
  return token;
}

export const createHiddenField = (name, value) => {
	var el = document.createElement('input')
	el.setAttribute('type', 'hidden')
	el.setAttribute('name', name)
	el.setAttribute('value', value)
	return el
}
