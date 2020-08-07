window.onload = function () {

  document.querySelector('#streamBtn').addEventListener('click', async function init(e) {
    try {
      const stream = await navigator.mediaDevices.getDisplayMedia({
        video: true
      })
      document.querySelector('#videoCont').srcObject = stream
    } catch (error) {
      alert(`${error.name}`)
      console.error(error)
    }
  })
}