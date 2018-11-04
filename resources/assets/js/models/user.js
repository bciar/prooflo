export default {

  bootstrap() {
    return axios.get('/api/bootstrap').then(response =>  {
      return response.data;
    }).catch(error => {
        return error.response;
    });
  }
}