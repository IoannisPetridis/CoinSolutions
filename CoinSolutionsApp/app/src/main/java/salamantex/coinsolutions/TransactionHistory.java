package salamantex.coinsolutions;


import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import com.ontbee.legacyforks.cn.pedant.SweetAlert.SweetAlertDialog;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

/**
 * Created by EEUser on 13/02/2018.
 */

public class TransactionHistory extends AppCompatActivity {

    Context activeContext;
    SweetAlertDialog pDialog;
    String transactionHistory;
    JSONArray transHistory;
    RecyclerView gridview;
    MyRecyclerAdapter myRecyclerAdapter;
    ArrayList<GridItem> mGridData;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.register_page);
        activeContext = this;
        transactionHistory = getIntent().getStringExtra("transactionHistory");
        try {
            transHistory = new JSONArray(transactionHistory);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        gridview = (RecyclerView) findViewById(R.id.recycler_view);
        mGridData = new ArrayList<GridItem>();
        refreshTransactions();
        gridview.setHasFixedSize(true);
        gridview.setAdapter(myRecyclerAdapter);
        final LinearLayoutManager linearLayoutManager = new LinearLayoutManager(this);
        //linearLayoutManager.setReverseLayout(true);
        gridview.setLayoutManager(linearLayoutManager);
        gridview.setItemAnimator(new DefaultItemAnimator());
        gridview.scrollToPosition(myRecyclerAdapter.getItemCount()-1);

    }

    @Override
    protected void onStart() {
        super.onStart();
    }

    @Override
    protected void onResume() {
        /*This is the state in which the app interacts with the user*/
        super.onResume();
    }

    @Override
    protected void onPause() {
        super.onPause();
        overridePendingTransition(0, 0);
    }

    @Override
    protected void onStop() {
        super.onStop();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent(activeContext, ProfilePage.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION);
        startActivity(intent);
        finish();
    }

    public void refreshTransactions() {
        for (int i=0; i<transHistory.length(); ++i) {
            JSONObject obj = null;
            try {
                obj = transHistory.getJSONObject(i);
                String source = obj.getString("Source_usr_email");
                String target = obj.getString("Target_usr_email");
                String amount = obj.getString("Cur_amount");
                String currency = obj.getString("Cur_type");
                String date = obj.getString("Timestamp_processed");
                GridItem item = new GridItem();
                item.setSource(source);
                item.setTarget(target);
                item.setAmount(amount);
                item.setCurrency(currency);
                item.setDate(date);
                mGridData.add(item);
            } catch (JSONException e) {
                e.printStackTrace();
            }

        }
        myRecyclerAdapter = new MyRecyclerAdapter(mGridData, R.layout.transaction_item_layout);
    }

}