import plotly.express as px

fig = px.pie(x=[0,1,2,3,4], y=[0,1,4,9,16])

div = fig.to_html(full_html=False)

with open ('file.html', 'w') as f:
    f.write(div)
