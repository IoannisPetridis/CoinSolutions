package salamantex.coinsolutions;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;


import java.util.ArrayList;


/**
 * Created by EEUser on 19/01/2018.
 */

public class MyRecyclerAdapter extends RecyclerView.Adapter<MyRecyclerAdapter.ViewHolder> {
    private ArrayList<GridItem> items;
    private int itemLayout;

    public MyRecyclerAdapter(ArrayList<GridItem> items, int itemLayout) {
        this.items = items;
        this.itemLayout = itemLayout;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(parent.getContext()).inflate(itemLayout, parent, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        GridItem item = items.get(position);
        holder.sourceTextView.setText(" By '"+item.getSource()+"'");
        holder.targetTextView.setText("To '"+item.getTarget()+"'");
        holder.amountTextView.setText(item.getAmount()+" "+item.getCurrency());
        holder.dateTextView.setText("Timestamp: "+item.getDate());
    }

    public int getItemCount() {
        return items.size();
    }

    public String getItemSource(int index) {
        return items.get(index).getSource();
    }

    public String getItemTarget(int index) {
        return items.get(index).getTarget();
    }

    public String getItemAmount(int index) {
        return items.get(index).getAmount();
    }

    public String getItemDate(int index) {
        return items.get(index).getDate();
    }

    public void add(GridItem item, int position) {
        items.add(position, item);
        notifyItemInserted(position);
    }

    public void remove(GridItem item) {
        int position = items.indexOf(item);
        items.remove(position);
        notifyItemRemoved(position);
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView sourceTextView;
        TextView targetTextView;
        TextView amountTextView;
        TextView dateTextView;

        public ViewHolder(View itemView) {
            super(itemView);
            sourceTextView = (TextView) itemView.findViewById(R.id.grid_item_source);
            targetTextView = (TextView) itemView.findViewById(R.id.grid_item_target);
            amountTextView = (TextView) itemView.findViewById(R.id.grid_item_amount);
            dateTextView = (TextView) itemView.findViewById(R.id.grid_item_date);
        }
    }
}
