document.addEventListener("DOMContentLoaded", (e) => {
  const apiUrl =
    "https://api.freecurrencyapi.com/v1/latest?apikey=fca_live_jxe0hpyS1ACv4HFLHJDlH9tUdBlBmpJt8V9N3wfl";

  const currencySelect = document.querySelector("#woo-currencies");

  currencySelect.addEventListener("change", async (e) => {
    try {
      const selectedCurrency = e.currentTarget.value.toUpperCase();
      const currencyExchangeRateData = await getData(apiUrl);

      const selectedCurrencyExchangeRate =
        currencyExchangeRateData.data[selectedCurrency];

      const formData = new FormData();

      formData.append("action", "modify_currency");
      formData.append("currency", selectedCurrency);
      formData.append("currencyExchangeRate", selectedCurrencyExchangeRate);

      if (window.modify_currency.post_id) {
        formData.append("postID", window.modify_currency.post_id);
      }

      let requestOptions = {
        method: "POST",
        body: formData,
        redirect: "follow",
      };

      try {
        const response = await fetch(
          "http://localhost:8888/wp-admin/admin-ajax.php",
          requestOptions
        );

        const result = await response.json();

        if (result.success) {
          location.reload();
        } else {
          alert(result.data.message);
        }
      } catch (error) {
        console.log(error.message);
      }
    } catch (error) {
      console.log(error.message);
    }
  });
});

const getData = async (apiUrl) => {
  try {
    const response = await fetch(apiUrl);

    if (!response.ok) {
      throw new Error(response.statusText);
    }

    const data = await response.json();

    return data;
  } catch (error) {
    console.log(error.message);
  }
};
