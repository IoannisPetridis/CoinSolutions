package salamantex.coinsolutions;

public class GridItem {

    private String source;
    private String target;
    private String amount;
    private String date;
    private String currency;

    public GridItem() {
        super();
    }


    public String getSource() {
        return source;
    }

    public void setSource(String source) {
        this.source = source;
    }

    public String getTarget() {
        return target;
    }

    public void setTarget(String target) {
        this.target = target;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) { this.amount = amount;}

    public String getDate() {
        return date;
    }

    public void setDate(String date) { this.date = date;}

    public String getCurrency() {
        return currency;
    }

    public void setCurrency(String currency) { this.currency = currency;}


}
