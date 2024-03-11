const http = async (endpoint) => {
  console.log('la url de la api es la siguiente...', import.meta.env.API_URL)
  const url = `${import.meta.env.API_URL}/wp-json/propiedades/${endpoint}`
  console.log('la url de la api es la siguiente...', url)
  const response = await fetch(url, {
    method: 'GET',
    redirect: 'follow'
  })

  if (!response) {
    console.log('error en la api')
    throw new Error('Network response was not ok')
  }

  const data = await response.json()
  return data
}

export default http
