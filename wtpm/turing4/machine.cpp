#include "machine.h"
#include <cstdlib>
#define FMT " %[a-zA-Z0-9_+*&~!-]"

bool machine::run()
{
  q=&vet[0];
  for(int i=0; apply(); ++i)
    {
      t->print();
      printf("%s\n", q->name.c_str());
      if (i>50000)
	return false;
    }
  return true;
}
void machine::debug()
{
  for(unsigned int i=0; i<vet.size(); ++i)
    {
      printf("%s {", vet[i].name.c_str());
      for(map<char, result>::iterator it = vet[i].action.begin(); it!=vet[i].action.end(); ++it)
	{
	  printf(" %d:%s;", it->first, (it->second).next->name.c_str());
	}
      printf("}\n");
    }
}
bool machine::apply()
{
  map<char, result>::iterator mit;
  map<char, result>& tmp=q->action;
  mit=tmp.find(t->get());
  if (mit==tmp.end())
    return false;
  t->set(mit->second);
  q=mit->second.next;
  if (t->pos<0)
    return false;
  return true;
}
void skip_comment(FILE* fin)
{
  char tmp[5];
  while (fscanf(fin, " %1[/]", tmp)==1)
    {
      if (fscanf(fin, "%1[*]", tmp)==1)
	{
	  while(1)
	    {
	      fscanf(fin, "%*[^*]");
	      fscanf(fin, "*");
	      if (fscanf(fin, "%1[/]", tmp)==1)
		break;
	    }
	}
      else
	fscanf(fin, "%*[^\n]");
    }
}
bool machine::read(FILE* fin)
{
  int n, f, sr, x;
  char name[256], s[5];
  map<string, int> tab;
  state *tmp;
  char r, w, dir;
  skip_comment(fin);

  int fscanf_retval;
  if ((fscanf_retval = fscanf(fin, " %d %d %d", &n, &f, &sr)) < 1)
    {
      printf("Syntax error:\n");
      printf("Expected number of states, and optionally the number of final states and subroutines.");
      return false;
    }

    // Estados
  for(int i=0; i<n; ++i)
    {
      skip_comment(fin);
      if (fscanf(fin, FMT, name)!=1)
	{
	  printf("Syntax error while parsing state %d name:\n", i);
	  printf("State name is invalid.");
	  return false;
	}
      if (tab.find(string(name))==tab.end())
	{
	  tmp=new state(string(name));
	  vet.push_back(*tmp);
	  tab.insert(make_pair(string(name), i));
	}
      else
	{
	  printf("Syntax error while parsing state %d name:\n", i);
	  printf("Two or more states with the same name %s.\n", name);
	  return false;
	}
    }

    // Estados finais
    if (fscanf_retval >= 2)
    {
        for (int i=0; i<f; ++i)
        {
            skip_comment(fin);
            if (fscanf(fin, FMT, name) != 1)
            {
                printf("Syntax error while parsing state %d name:\n", i);
                printf("State name is invalid.");
                return false;
            }
            else if (tab.find(string(name)) == tab.end())
            {
              printf("Syntax error while parsing state %d name:\n", i);
              printf("State not found: %s.\n", name);
              return false;
            }
            vet[tab.find(string(name))->second].final = true;
        }
    }

    // Subrotinas
    if (fscanf_retval >= 3)
    {
        for (int i=0; i<sr; ++i)
        {
            int srn;
            skip_comment(fin);
            if (fscanf(fin, "%s %d", name, &srn) != 2)
            {
                printf("Syntax error while parsing subroutine %d:\n", i);
                printf("Expected: <routine name> <number_of_states> <state> <state> <state>\n");
                return false;
            }

            char srname[256];
            for (int j=0; j<srn; ++j)
            {
                if (fscanf(fin, FMT, srname) != 1 || tab.find(string(srname)) == tab.end())
                {
                    printf("Syntax error while parsing subroutine %d:\n", i);
                    printf("Expected: <routine name>: <number_of_states> <state> <state> <state>\n");
                    printf("At state: %d\n", j);
                    return false;
                }

                vet[tab.find(string(srname))->second].subroutine = string(name);
            }
        }
    }

  for(int i=0; i<n; ++i)
    {
      skip_comment(fin);
      if (fscanf(fin, FMT, name)!=1)
	{
	  printf("Syntax error while parsing state %d description:\n", i);
	  printf("Invalid name.\n");
	  return false;
	}
      if (tab.find(string(name))==tab.end())
	{
	  printf("Syntax error while parsing state %d description:\n", i);
	  printf("Uknown state %s.\n", name);
	  return false;
	}
      x=tab[string(name)];
      skip_comment(fin);
      if (fscanf(fin, " %1[{]", s)!=1)
	{
	  printf("Syntax error while parsing state %d description:\n", i);
	  printf("Expected \'{\'\n");
	  return false;
	}
      for(int j=0;;++j)
	{
	  skip_comment(fin);
	  fscanf(fin, " %c", &r);
	  if (r=='}')
	    break;
	  if (r==':')
	    r=' ';
	  else
	    {
	      skip_comment(fin);
	      if (fscanf(fin, " %1[:]", s)!=1)
		{
		  printf("Syntax error while parsing state %d description:\n", i);
		  printf("At %d transition, expected \':\'\n", j);
		  return false;
		}
	    }
	  skip_comment(fin);
	  fscanf(fin, " %c", &w);
	  if (w==',')
	    w=' ';
	  else
	    {
	      skip_comment(fin);
	      if (fscanf(fin, " %1[,]", s)!=1)
		{
		  printf("Syntax error while parsing state %d description:\n", i);
		  printf("At %d transition, expected \',\'\n", j);
		  return false;
		}
	    }
	  skip_comment(fin);
	  if (fscanf(fin, FMT, name)!=1)
	    {
	      printf("Syntax error while parsing state %d description:\n", i);
	      printf("At %d transition, invalid destination name.\n", j);
	      return false;
	    }
	  else if (tab.find(string(name))==tab.end())
	    {
	      printf("Syntax error while parsing state %d description:\n", i);
	      printf("At %d transition, unknown state %s.\n", j, name);
	      return false;
	    }
	  skip_comment(fin);
	  if (fscanf(fin, " %1[,]", s)!=1)
	    {
	      printf("Syntax error while parsing state %d description:\n", i);
	      printf("At %d transition, expected \',\'\n", j);
	      return false;
	    }
	  skip_comment(fin);
	  if (fscanf(fin, " %1[RL]", s)!=1)
	    {
	      printf("Syntax error while parsing state %d description:\n", i);
	      printf("At %d transition, expected \'R\' or \'L\'\n", j);
	      return false;
	    }
	  dir=s[0];
	  skip_comment(fin);
	  if (fscanf(fin, " %1[;]", s)!=1)
	    {
	      printf("Syntax error while parsing state %d description:\n", i);
	      printf("At %d transition, expected \';\'\n", j);
	      return false;
	    }
	  vet[x].action.insert(make_pair(r,result(dir,&vet[tab[string(name)]],w)));
	}
    }
  return true;
}
machine::machine(tape* ta)
{
  t=ta;
}

char toGraphLetter(const char c)
{
    if (c == ' ') return 'B';
    else return c;
}

char toGraphDir(int dir)
{
    if (dir < 0) return 'E';
    else return 'D';
}

#ifdef GRAPH_GEN

// TODO:
bool machine::hasOuterEdges(const state* s)
{
    for (vector<state>::const_iterator it = this->vet.begin(); it != this->vet.end(); ++it)
    {
        for (map<char,result>::const_iterator t = it->action.begin(); t != it->action.end(); ++t)
        {
            if (t->second.next == s || &(*it) == s)
            {
                if (t->second.next->subroutine != it->subroutine)
                {
                    return true;
                }
            }
        }
    }

    return false;
}


void machine::generateGraph(FILE*f, const char *ratio, bool lr, bool dl, bool ef)
{
    fprintf(f, "digraph G {\n");
    if (ratio) fprintf(f, " ratio=\"%s\";\n", ratio);
    if (lr) fprintf(f, "  rankdir=LR;\n");

    map<string, string> outputPerGraph;

    map<const state*, int> stateToIndexMap;

    for (unsigned count = 0; count < this->vet.size(); ++count)
    {
        stateToIndexMap[&(this->vet[count])] = count;
        char buffer[4096];
        char sBuffer[4096];
        sprintf(buffer, "  q%d [label=\"%s\",peripheries=%d%s];\n", count, this->vet[count].name.c_str(), this->vet[count].final ? 2 : 1, count ? "" : ",style=bold");
        sprintf(sBuffer, "  sq%d [label=\"%s\",peripheries=%d%s];\n", count, this->vet[count].name.c_str(), this->vet[count].final ? 2 : 1, count ? "" : ",style=bold");

        if (this->vet[count].subroutine == string())
        {
            outputPerGraph["<graph>"] += string(buffer);
        }
        else
        {
            if (ef)
            {
                outputPerGraph[this->vet[count].subroutine + " (detailed)"] += string(sBuffer);
            }
            if (this->hasOuterEdges(&this->vet[count]))
            {
                outputPerGraph[this->vet[count].subroutine] += string(buffer);
            }
        }
    }

    for (vector<state>::const_iterator i = this->vet.begin(); i != this->vet.end(); ++i)
    {
        map<const state*, vector<pair<char, result> > > transitionToTargetStateMap;

        for (map<char, result>::const_iterator j = i->action.begin(); j != i->action.end(); ++j)
        {
            transitionToTargetStateMap[j->second.next].push_back(pair<char, result>(j->first, j->second));
        }

        for (map<const state*, vector<pair<char, result> > >::const_iterator j = transitionToTargetStateMap.begin();
             j != transitionToTargetStateMap.end();
             ++j)
        {
            char transitions[4096];
            char nextTransition[1024];
            char buffer[4096];
            char sBuffer[4096];
            transitions[0] = '\0';

            for (vector<pair<char, result> >::const_iterator k = j->second.begin(); k != j->second.end(); ++k)
            {
                sprintf(nextTransition, "%c/%c, %c\\n", toGraphLetter(k->first), toGraphLetter(k->second.insert), toGraphDir(k->second.dir));
                strcat(transitions, nextTransition);
            }

            sprintf(buffer, "  q%d -> q%d [label=\"%s\",decorate=%s,labelangle=0];\n", stateToIndexMap[&(*i)], stateToIndexMap[j->first], transitions, dl ? "true" : "false");
            sprintf(sBuffer, "  sq%d -> sq%d [label=\"%s\",decorate=%s,labelangle=0];\n", stateToIndexMap[&(*i)], stateToIndexMap[j->first], transitions, dl ? "true" : "false");

            if (i->subroutine == j->first->subroutine && i->subroutine != string())
            {
                if (!ef)
                {
                    outputPerGraph[i->subroutine] += string(buffer);
                }
                else
                {
                    outputPerGraph[i->subroutine + " (detailed)"] += string(sBuffer);
                }
            }
            else
            {
                outputPerGraph["<graph>"] += string(buffer);
            }
        }
    }

    int count = 0;
    for (map<string, string>::const_iterator i = outputPerGraph.begin(); i != outputPerGraph.end(); ++i)
    {
        if (i->first != "<graph>")
        {
            fprintf(f, "subgraph cluster%d {\n", count++);
            fprintf(f, "  node [style=filled];\n");
            fprintf(f, "  label=\"%s\";\n", i->first.c_str());
            fprintf(f, "  color=black;\n");
            fprintf(f, "%s\n", i->second.c_str());
            fprintf(f, "}\n");
        }
    }

    fprintf(f, "%s\n", outputPerGraph["<graph>"].c_str());

    fprintf(f, "}\n");
}
#endif
