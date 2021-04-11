import plotly
import numpy as np
import plotly.graph_objs as go


t = np.linspace(0, 2*np.pi, num=1000)
trace1 = go.Scatter(
        x = np.cos(t) / (np.sin(t)**2 + 1),
        y = np.cos(t) * np.sin(t) / (np.sin(t)**2 +1)
)

#trace2 = go.Scatter(
#        x = t, 
#        y = t**2,
#)

data = [trace1]
activeshape = go.layout.Activeshape(fillcolor="black")
#margin = go.layout.Margin(l=1, r=1, b=1, t=1)
modebar = go.layout.Modebar(bgcolor="rgba(0,0,0,0)", color="rgb(40,54,24)")
legend = go.layout.Legend(bgcolor='rgba(0,0,0,0)',borderwidth=2,bordercolor="white")
font = go.layout.Font(family="Open Sans")

layout = go.Layout(
        activeshape=activeshape,
#        margin=margin,
        modebar=modebar,
        legend=legend,
        paper_bgcolor='rgba(0, 0, 0, 0)',
        plot_bgcolor='rgba(165, 165, 141, 0.9)',
        font=font,
        xaxis=dict(autorange=True,mirror=True,ticks='outside',showline=True,gridcolor="black",zerolinecolor="black"),
        yaxis=dict(autorange=True,mirror=True,ticks='outside',showline=True,gridcolor="black",zerolinecolor="black"),
        width=1000,
        height=1000
        )

fig = go.Figure(data=data, layout=layout)

print(plotly.offline.plot(fig, include_plotlyjs=False, output_type='div'))

